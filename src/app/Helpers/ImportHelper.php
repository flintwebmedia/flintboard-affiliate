<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Helpers;

use FlintWebmedia\FlintboardAffiliate\app\Models\Attribute;
use FlintWebmedia\FlintboardAffiliate\app\Models\Feed;
use FlintWebmedia\FlintboardAffiliate\app\Models\Mapping;
use FlintWebmedia\FlintboardAffiliate\app\Models\Product;
use FlintWebmedia\FlintboardAffiliate\app\Models\Value;
use Illuminate\Support\Facades\Validator;

class ImportHelper {

    public $feed;
    public $fields;
    public $products;
    public $feed_id;
    public $feed_csv;
    protected $instantiateTime;

    public function __construct($instantiateTime = '') {
        $this->instantiateTime = $instantiateTime;
    }

    /**
     * Get feed CSV from Feed model URL
     *
     * @return bool|Resource
     */
    public function getFeedCSV() {
        $this->feed = Feed::find($this->feed_id);
        $feed_url = $this->feed->url;

        $this->feed_csv = fopen($feed_url, 'r');

        if($this->feed_csv) {
            return $this->feed_csv;
        }

        return false;
    }

    /**
     * Get fields from firstline of feed CSV file
     *
     * @return bool
     */
    public function getFieldsFromFeed($delimiter = ';')
    {
        $index = 0;
        $this->fields = collect();

        $feed_line = fgetcsv($this->feed_csv, 0, $delimiter);

        foreach($feed_line as $i => $item) {
            $this->fields[$i] = $item;
        }

        if(!empty($this->fields)) {
            return $this->fields;
        }

        return false;
    }

    /**
     * @param $feed_csv
     * @param string $delimiter
     * @return bool
     */
    public function getProductsFromFeed($delimiter = ';')
    {
        $index = 0;
        $this->products = collect();

        while(($line = fgetcsv($this->feed_csv, 0, $delimiter)) !== false) {
            if($index !== 0) {
                $mappedLine = collect($line);
                $mappedLine = $this->fields->combine($mappedLine);

                $this->products[] = $mappedLine;
            }

            $index++;
        }

        if(!empty($this->products)) {
            return $this->products;
        }

        return false;
    }

    /**
     * @param mixed $feed_id
     */
    public function setFeedId($feed_id)
    {
        $this->feed_id = $feed_id;
    }

    /**
     * @return mixed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Array['id' => 'name'] for select input
     */
    public function getAttributesArray() {

        // Get all default product attributes which can be assigned
        $product = new Product();
        $product_attributes = $product->getFillable();

        // Combine product attributes so key and name are equal
        $product_attributes = array_combine($product_attributes, $product_attributes);

        // Create attributes array. First index is null so skip this field, then add product attributes
        $attributes = [null => ''];
        $attributes = array_merge($attributes, $product_attributes);

        // Get all administrator-made attributes
        $productAttributes = Attribute::all();

        // Add all product attributes from Attribute model
        foreach($productAttributes as $attribute) {
            $attributes[strtolower($attribute->name)] = strtolower($attribute->name);
        }

        return $attributes;
    }

    /**
     * @param string $field
     * @param string $attribute
     * @param null $feed_id
     * @return Mapping|string
     */
    public function addNewMapping($field = '', $attribute = '', $feed_id = null)
    {
        if($feed_id !== null) {
            $this->feed_id = $feed_id;
        }

        $validator = Validator::make([
            'feed_id' => $this->feed_id,
            'field' => $field
        ], [
            'feed_id' => 'required|unique_with:mappings,field',
            'field' => 'required'
        ]);

        // If validator fails, check if a mapping of the same field on the same feed exists then respond.
        if($validator->fails()) {
            $mapping = Mapping::where('feed_id', $this->feed_id)
                ->where('field', $field)
                ->first();

            // If mapping with same field on same feed is found
            if($mapping->count()) {
                // If mapping also has equal attribute, just return mapping
                if($mapping->attribute === $attribute) {
                    return $mapping;
                }

                // Otherwise, update attribute
                $mapping->attribute = $attribute;
                return $mapping;
            }
        }

        // If no mapping was found, create one
        $mapping = new Mapping([
            'field' => $field,
            'attribute' => $attribute,
            'feed_id' => $this->feed_id
        ]);

        return $mapping;

    }

    public function getAttributeFromMapping($field = '', $feed_id = 0)
    {
        if($feed_id === 0) {
            $feed_id = $this->feed_id;
        }

        $mapping = Mapping::where('feed_id', $this->feed_id)
            ->where('field', $field)
            ->first();

        // If a mapping has been found for this field on this feed_id
        if($mapping) {
            $attribute = $mapping->attribute;

            return $attribute;
        }

        return false;
    }

    /**
     * Create new product in database. Use mappings and Value model to create custom attributes
     *
     * @param $product
     * @return Product|bool
     */
    public function addNewProduct($product)
    {
        // Array to sotre mappable field-attributes on product
        $newProductData = ['feed_id' => $this->feed_id];
        $newValues = collect();

        // Loop through all fields in feed product line
        foreach($product as $field => $attribute) {
            // If the feed product field is mapped
            if($attribute = $this->getAttributeFromMapping($field)) {
                $attributeEAV = Attribute::where('name', $attribute)->first();

                // If field is mapped to custom attribute, create new Value with attribute ID and value
                if($attributeEAV && $attributeEAV->count()) {
                    $newValues[] = new Value([
                        'attribute_id' => $attributeEAV->id,
                        'value' => $product[$field]
                    ]);
                }

                // Add new product data to array to create new Product model
                $newProductData[$attribute] = $product[$field];
            }
        }

        // Create new Product model
        $product = new Product($newProductData);

        // If a product with same product_id exists, delete it. No updating in case mapping changed!
        if(!empty($newProductData['product_id'])) {
            if($oldProducts = Product::where('product_id', $newProductData['product_id'])->get()) {
                foreach($oldProducts as $oldProduct) {
                    $oldProduct->delete();
                }
            }
        }

        // Save product, then store product id in Values
        if($product->save()) {
            foreach($newValues as $newValue) {
                $newValue->product_id = $product->id;
                $newValue->save();
            }

            return $product;
        }

        return false;
    }

}