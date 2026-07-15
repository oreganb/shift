/**
 * Template Name: Simple - Responsive Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): Datatables Export Data
 * Version: 3.0.0
 */

document.addEventListener('DOMContentLoaded', () => {
    const exportDataTable = document.querySelector('[data-tables="export-data"]');
    if (exportDataTable) {
        new DataTable(exportDataTable, {
            dom: "<'d-md-flex justify-content-between align-items-center my-2'Bf>" +
                "rt" +
                "<'d-md-flex justify-content-between align-items-center mt-2'ip>",
            responsive: true,
            buttons: [
                { extend: 'copy', className: 'btn btn-sm btn-primary' },
                { extend: 'csv', className: 'btn btn-sm btn-primary active' },
                { extend: 'excel', className: 'btn btn-sm btn-primary' },
                { extend: 'print', className: 'btn btn-sm btn-primary active' },
                { extend: 'pdf', className: 'btn btn-sm btn-primary' }
            ],
            language: {
                paginate: {
                    first: '<i class="ti ti-chevrons-left"></i>',
                    previous: '<i class="ti ti-chevron-left"></i>',
                    next: '<i class="ti ti-chevron-right"></i>',
                    last: '<i class="ti ti-chevrons-right"></i>'
                }
            },
            initComplete: function () {
                setTimeout(() => {
                    document.querySelectorAll('.dt-buttons .btn-secondary').forEach(btn => {
                        btn.classList.remove('btn-secondary');
                        btn.classList.add('btn-primary');
                    });
                }, 50);
            }
        });
    }

    const exportDropdownTable = document.querySelector('[data-tables="export-data-dropdown"]');
    if (exportDropdownTable) {
        new DataTable(exportDropdownTable, {
            dom: "<'d-md-flex justify-content-between align-items-center my-2'<'dropdown'B>f>" +
                "rt" +
                "<'d-md-flex justify-content-between align-items-center mt-2'ip>",
            responsive: true,
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="ti ti-download me-1"></i> Export',
                    className: 'btn btn-sm btn-primary dropdown-toggle drop-arrow-none',
                    autoClose: true,
                    buttons: [
                        {
                            extend: 'copy',
                            text: '<i class="ti ti-copy me-1 fs-lg align-middle"></i> Copy',
                            className: 'dropdown-item'
                        },
                        {
                            extend: 'csv',
                            text: '<i class="ti ti-file-type-csv me-1 fs-lg align-middle"></i> CSV',
                            className: 'dropdown-item'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="ti ti-file-spreadsheet me-1 fs-lg align-middle"></i> Excel',
                            className: 'dropdown-item'
                        },
                        {
                            extend: 'print',
                            text: '<i class="ti ti-printer me-1 fs-lg align-middle"></i> Print',
                            className: 'dropdown-item'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="ti ti-file-text me-1 fs-lg align-middle"></i> PDF',
                            className: 'dropdown-item'
                        }
                    ]
                }
            ],
            language: {
                paginate: {
                    first: '<i class="ti ti-chevrons-left"></i>',
                    previous: '<i class="ti ti-chevron-left"></i>',
                    next: '<i class="ti ti-chevron-right"></i>',
                    last: '<i class="ti ti-chevrons-right"></i>'
                }
            },
            initComplete: function () {
                setTimeout(() => {
                    document.querySelectorAll('.dt-buttons .btn-secondary').forEach(btn => {
                        btn.classList.remove('btn-secondary');
                        btn.classList.add('btn-primary');
                    });
                }, 50);
            }
        });
    }
});