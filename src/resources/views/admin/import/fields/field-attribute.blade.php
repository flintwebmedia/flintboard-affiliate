<div class="form-group">
    <?php
    $defaultValue = $field;
        if(count($previousMappings->where('field', $field))) {
            $defaultValue = $previousMappings->where('field', $field)->first()->attribute;
        }
    ?>
    {!! Form::label($field, $field) !!}
    {!! Form::select('fields[' . $field . ']', $importHelper->getAttributesArray(), $defaultValue, ['class' => 'form-control']) !!}
</div>
