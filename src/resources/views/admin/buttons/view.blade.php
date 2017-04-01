@if ($crud->hasAccess('import'))
	<a href="{{ url($crud->route.'/'.$entry->getKey()) }}/import" class="btn btn-xs btn-default"><i class="fa fa-glass"></i> View</a>
@endif