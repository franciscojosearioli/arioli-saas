@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center">
        @can('medicacion_create')
            <div style="margin-bottom: 10px;" class="row">
                <div class="col-lg-12">
                    <a class="btn btn-success" href="{{ route('admin.medicacion.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.medicacion.title_singular') }}
                    </a>
                </div>
            </div>
        @endcan
        
        <div style="margin-bottom: 10px;" class="row">
                <div class="col-lg-12">
        <a class="btn btn-primary" href="{{ route('admin.medicacion.esquema') }}" target="_blank">
            Esquema de medicacion
        </a>
                </div>
            </div>
    </div>

<div class="card">
    <div class="card-header">
        {{ trans('cruds.medicacion.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Medicacion">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.medicacion.fields.paciente') }}
                        </th>
                        <th>
                            {{ trans('cruds.medicacion.fields.medicamento') }}
                        </th>
                        <th>
                            {{ trans('cruds.medicacion.fields.cantidad') }}
                        </th>
                        <th>
                            {{ trans('cruds.medicacion.fields.horario') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Medicaciones as $key => $Medicacion)
                        <tr data-entry-id="{{ $Medicacion->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $Medicacion->paciente->nombre ?? '' }} {{ $Medicacion->paciente->apellido ?? '' }}
                            </td>
                            <td>
                                {{ $Medicacion->medicamento ?? '' }}
                            </td>
                            <td>
                                {{ $Medicacion->cantidad ?? '' }} {{ $Medicacion->unidad ?? '' }}
                            </td>
                            <td>
                                {{ $Medicacion->horario ?? '' }}
                            </td>
                            <td>
                                @can('medicacion_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.medicacion.show', $Medicacion->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('medicacion_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.medicacion.edit', $Medicacion->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('medicacion_delete')
                                    <form action="{{ route('admin.medicacion.destroy', $Medicacion->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('medicacion_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.medicacion.massDestroy') }}",
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
  let table = $('.datatable-Medicacion:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection