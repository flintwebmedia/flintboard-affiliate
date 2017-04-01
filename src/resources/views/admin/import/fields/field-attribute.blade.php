<div class="form-group">
    {!! Form::label($field, $field) !!}
    {!! Form::select('fields[' . $field . ']', $importHelper->getAttributesArray(), $field, ['class' => 'form-control']) !!}
</div>
