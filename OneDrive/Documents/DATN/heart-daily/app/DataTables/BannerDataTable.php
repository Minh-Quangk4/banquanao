<?php

namespace App\DataTables;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BannerDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($query) {
                $editBtn = "<a class='btn btn-primary' href='" . route('admin.banners.edit', $query->id) . "'><i class='far fa-edit'></i></a>";
                $deleteBtn = "<a class='btn btn-danger delete-item ml-2' href='" . route('admin.banners.destroy', $query->id) . "'><i class='far fa-trash-alt'></i></a>";
                return $editBtn . $deleteBtn;
            })
            ->addColumn('image', function ($query) {
                return $img = "<img width='100px' src='" . Storage::url($query->image) . "'>";
            })
            ->addColumn('status', function ($query) {
                if ($query->status == 1) {
                    $button = "<label class='custom-switch mt-2'>
                        <input type='checkbox' data-id='" . $query->id . "' checked name='custom-switch-checkbox' class='custom-switch-input change-status'>
                        <span class='custom-switch-indicator'></span>
                      </label>";
                } else {
                    $button = "<label class='custom-switch mt-2'>
                        <input type='checkbox' data-id='" . $query->id . "' name='custom-switch-checkbox' class='custom-switch-input change-status'>
                        <span class='custom-switch-indicator'></span>
                      </label>";
                }
                return $button;
            })

            ->rawColumns(['action', 'image', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Banner $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('banner-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->width(100),
            Column::make('image')->width(200),
            Column::make('url')->width(200),
            Column::make('status')->width(150),
            Column::computed('action')
                ->exportable(flag: false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Banner_' . date('YmdHis');
    }
}
