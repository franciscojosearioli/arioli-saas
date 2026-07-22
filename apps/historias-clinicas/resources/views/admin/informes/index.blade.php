@extends('layouts.admin')
@section('content')
@can('informe_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.informe.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.informe.title_singular') }}
            </a>
            
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.informe.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Informe">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.informe.fields.fecha') }}
                        </th>
                        <th>
                            {{ trans('cruds.informe.fields.tipo') }}
                        </th>
                        <th>
                            {{ trans('cruds.informe.fields.paciente') }}
                        </th>
                        <th>
                            {{ trans('cruds.informe.fields.document_file') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Informes as $key => $Informe)
                        <tr data-entry-id="{{ $Informe->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $Informe->fecha ?? '' }}
                            </td>
                            <td>
                                {{ $Informe->tipo->name ?? '' }}
                            </td>
                            <td>
                                {{ $Informe->paciente->nombre ?? '' }} {{ $Informe->paciente->apellido ?? '' }}
                            </td>
                            <td>
                                
                                @if($Informe->document_files)
                                    @php
                                        $files = json_decode($Informe->document_files);
                                    @endphp

                                    @if(is_array($files) || is_object($files))
                                        @foreach($files as $file)
                                        <a href="{{ asset('storage/uploads/' . $Informe->paciente->id . '/' . $Informe->tipo->id . '/' . $file) }}" target="_blank">
                                                Ver Informe
                                            </a>
                                            <br>
                                        @endforeach
                                    @else
                                        {{ trans('global.no_file_attached') }}
                                    @endif
                                @else
                                    {{ trans('global.no_file_attached') }}
                                @endif
                            </td>
                            <td>

                                @can('informe_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.informe.show', $Informe->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('informe_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.informe.edit', $Informe->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('informe_delete')
                                    <form action="{{ route('admin.informe.destroy', $Informe->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('informe_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.informe.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Informe:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection