<!-- JAVASCRIPT -->
<script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/libs/metismenu/metisMenu.min.js')}}"></script>
<script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

@if(!request()->routeIs('monthly.sheet'))
<!--tinymce js-->
<script src="{{asset('assets/libs/tinymce/tinymce.min.js')}}"></script>


<!-- dom autoscroll -->
<script src="{{asset('assets/libs/dom-autoscroller/dom-autoscroller.min.j')}}s"></script>
<!-- apexcharts -->
{{-- <script src="{{asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('assets/js/pages/dashboard.init.js')}}"></script> --}}
@endif
<!-- Required datatable js -->
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
<!-- Responsive examples -->
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
@if(!request()->routeIs('monthly.sheet'))
<!-- dragula init js -->
<script src="{{asset('assets/libs/dragula/dragula.min.js')}}"></script>
<!-- contact init js -->
<script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
<!-- Datatable init js -->
<script src="{{asset('assets/js/pages/datatables-base.init.js')}}"></script>
<!--taks-kanban-->
<script src="{{asset('assets/js/pages/kanban.init.js')}}"></script>
<!-- for basic area chart -->
{{-- <script src="https://apexcharts.com/samples/assets/stock-prices.js')}}"></script>
<!-- for github style chart -->
<script src="https://apexcharts.com/samples/assets/github-data.js')}}"></script>
<!-- for irregular timeseries chart -->
<script src="https://apexcharts.com/samples/assets/irregular-data-series.js')}}"></script> --}}
@endif
<script src="{{asset('assets/libs/moment/moment.js')}}"></script>
@if(!request()->routeIs('monthly.sheet'))
<!-- areacharts init -->
{{-- <script src="{{asset('assets/js/pages/apexcharts-area.init.js')}}"></script>
<!-- barcharts init -->
<script src="{{asset('assets/js/pages/apexcharts-bar.init.js')}}"></script>
<!-- boxplotcharts init -->
<script src="{{asset('assets/js/pages/apexcharts-boxplot.init.js')}}"></script>
<!-- bubblecharts init -->
<script src="{{asset('assets/js/pages/apexcharts-bubble.init.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.8.17/dayjs.min.js"></script>
<script src="https://apexcharts.com/samples/assets/ohlc.js"></script>
<!-- candlestick charts init -->
<script src="{{asset('assets/js/pages/apexcharts-candlestick.init.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.0/plugin/quarterOfYear.min.js"></script>
<!-- apexcharts init -->
<script src="{{asset('assets/js/pages/apexcharts-column.init.js')}}"></script>
<script src="{{asset('assets/js/pages/apexcharts-heatmap.init.js')}}"></script>
<!-- linecharts init -->
<script src="{{asset('assets/js/pages/apexcharts-line.init.js')}}"></script>
<!-- mixed charts init -->
<script src="{{asset('assets/js/pages/apexcharts-mixed.init.js')}}"></script>
<!-- radarcharts init -->
<script src="{{asset('assets/js/pages/apexcharts-radar.init.js')}}"></script>
<!-- radialbar charts init -->
<script src="{{asset('assets/js/pages/apexcharts-radialbar.init.js')}}"></script>
<script src="{{asset('assets/js/pages/apexcharts-pie.init.js')}}"></script>
<!-- polarareacharts init -->
<script src="{{asset('assets/js/pages/apexcharts-polararea.init.js')}}"></script>
<!-- radarcharts init -->
<script src="{{asset('assets/js/pages/apexcharts-range.init.js')}}"></script>
<!-- scatter charts init -->
<script src="{{asset('assets/js/pages/apexcharts-scatter.init.js')}}"></script>
<!-- timeline charts init -->
<script src="{{asset('assets/js/pages/apexcharts-timeline.init.js')}}"></script> --}}
<!-- Datatable init js -->
<script src="{{asset('assets/js/pages/datatables-advanced.init.js')}}"></script>
@endif
<!-- buttons examples -->
<script src="{{asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>
<!-- autofill examples -->
<script src="{{asset('assets/libs/datatables.net-autofill/js/dataTables.autoFill.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-autofill-bs5/js/autoFill.bootstrap5.min.js')}}"></script>
@if(!request()->routeIs('monthly.sheet'))
<!-- Datatable init js -->
<script src="{{asset('assets/js/pages/datatables-extension.init.js')}}"></script>
@endif
<!-- buttons examples -->
<script src="{{asset('assets/libs/jszip/jszip.min.js')}}"></script>
<script src="{{asset('assets/libs/pdfmake/build/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/libs/pdfmake/build/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<!-- autofill examples -->
<script src="{{asset('assets/libs/datatables.net-colreorder/js/dataTables.colReorder.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-colreorder-bs5/js/colReorder.bootstrap5.min.js')}}"></script>
<!-- fixedcolumns examples -->
<script src="{{asset('assets/libs/datatables.net-fixedcolumns/js/dataTables.fixedColumns.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js')}}"></script>
<!-- fixedheader examples -->
<script src="{{asset('assets/libs/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-fixedheader-bs5/js/fixedHeader.bootstrap5.min.js')}}"></script>
<!-- keyTable examples -->
<script src="{{asset('assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-keytable-bs5/js/keyTable.bootstrap5.min.js')}}"></script>
<!-- rowgroup examples -->
<script src="{{asset('assets/libs/datatables.net-rowgroup/js/dataTables.rowGroup.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-rowgroup-bs5/js/rowGroup.bootstrap5.min.js')}}"></script>
<!-- rowReorder examples -->
<script src="{{asset('assets/libs/datatables.net-rowreorder/js/dataTables.rowReorder.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-rowreorder-bs5/js/rowReorder.bootstrap5.min.js')}}"></script>
<!-- scroller examples -->
<script src="{{asset('assets/libs/datatables.net-scroller/js/dataTables.scroller.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-scroller-bs5/js/scroller.bootstrap5.min.js')}}"></script>
<!-- searchpanes examples -->
<script src="{{asset('assets/libs/datatables.net-searchpanes/js/dataTables.searchPanes.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-searchpanes-bs5/js/searchPanes.bootstrap5.min.js')}}"></script>
<!-- select example -->
<script src="{{asset('assets/libs/datatables.net-select/js/dataTables.select.min.js')}}"></script>
<!-- select examples -->
<script src="{{asset('assets/libs/datatables.net-select-bs5/js/select.bootstrap5.min.js')}}"></script>


<!-- bs custom file input plugin -->
<script src="{{asset('assets/libs/autosize/autosize.min.js')}}"></script>
<!-- bs custom file input plugin -->
<script src="{{asset('assets/libs/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
@if(!request()->routeIs('monthly.sheet'))
<script src="{{asset('assets/js/pages/form-element.init.js')}}"></script>


<!-- bs custom file input plugin -->
<script src="{{asset('assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
<script src="{{asset('assets/js/pages/form-bs-maxlength.init.js')}}"></script>


<!-- init js -->
<script src="{{asset('assets/js/pages/form-editor.init.js')}}"></script>
@endif
<!-- App js -->
<script src="{{asset('assets/js/app.js')}}"></script>

<!-- Bootstrap datepicker -->
<script src="{{asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
@if(!request()->routeIs('monthly.sheet'))
<script src="{{asset('assets/js/pages/form-datepicker.init.js')}}"></script>
<!-- Bootstrap datepicker -->
<script src="{{asset('assets/libs/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('assets/js/pages/form-datetimepicker.init.js')}}"></script>
<!-- form mask -->
<script src="{{asset('assets/libs/inputmask/jquery.inputmask.min.js')}}"></script>

<!-- form mask init -->
<script src="{{asset('assets/js/pages/form-mask.init.js')}}"></script>
@else
<!-- Bootstrap datepicker -->
<script src="{{asset('assets/libs/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js')}}"></script>
<!-- form mask -->
<script src="{{asset('assets/libs/inputmask/jquery.inputmask.min.js')}}"></script>
@endif
<!-- Bootstrap datepicker -->
<script src="{{asset('assets/libs/moment/min/moment.min.js')}}"></script>
<script src="{{asset('assets/libs/daterangepicker/daterangepicker.js')}}"></script>

@if(!request()->routeIs('monthly.sheet'))
<script src="{{asset('assets/js/pages/form-rangepicker.init.js')}}"></script>
<!-- Ion Range Slider-->
<script src="{{asset('assets/libs/ion-rangeslider/js/ion.rangeSlider.min.js')}}"></script>

<!-- Range slider init js-->
<script src="{{asset('assets/js/pages/range-sliders.init.js')}}"></script>
<script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>

<script src="{{asset('assets/js/pages/form-select2.init.js')}}"></script>
<!-- touchspin js -->
<script src="{{asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>

<script src="{{asset('assets/js/pages/form-touchspin.init.js')}}"></script>
<script src="{{asset('assets/libs/typeahead.js/typeahead.bundle.min.js')}}"></script>
<script src="{{asset('assets/js/pages/form-typeahead.init.js')}}"></script>
<script src="{{asset('assets/libs/parsleyjs/parsley.min.js')}}"></script>

<script src="{{asset('assets/js/pages/form-validation.init.js')}}"></script>
<!-- fontawesome icons init -->
<script src="{{asset('assets/js/pages/fontawesome.init.js')}}"></script>
<!-- materialdesign icon js-->
<script src="{{asset('assets/js/pages/materialdesign.init.js')}}"></script>
@else
<script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/parsleyjs/parsley.min.js')}}"></script>
@endif
<!-- blockUI -->
<script src="{{asset('assets/libs/block-ui/jquery.blockUI.js')}}"></script>

<!-- blockUI init -->
<script src="{{asset('assets/js/pages/jquery.blockUI.init.js')}}"></script>
@if(!request()->routeIs('monthly.sheet'))
 <!-- sortablejs -->
 <script src="{{asset('assets/libs/sortablejs/Sortable.min.js')}}"></script>

<!-- nestable init js -->
<script src="{{asset('assets/js/pages/card-draggble.init.js')}}"></script>
<!-- masonry pkgd -->
<script src="{{asset('assets/libs/masonry-layout/masonry.pkgd.min.js')}}"></script>
<!-- contextMenu js-->
<script src="{{asset('assets/libs/jquery-contextmenu-js/jquery.contextMenu.min.js')}}"></script>

<!-- contextMenu init js-->
<script src="{{asset('assets/js/pages/contextMenu.init.js')}}"></script>
<!-- slick-carousel js -->
<script src="{{asset('assets/libs/slick-carousel/slick/slick.min.js')}}"></script>

<!-- slick-carousel init js -->
<script src="{{asset('assets/js/pages/slick-carousel.init.js')}}"></script>

    <!-- nestable init js -->
    <script src="{{asset('assets/js/pages/sortable.init.js')}}"></script>
@endif
    <!-- Sweet Alerts js -->
<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>

@if(!request()->routeIs('monthly.sheet'))
<!-- Sweet alert init js-->
<script src="{{asset('assets/js/pages/sweet-alerts.init.js')}}"></script>
<!-- Toaster Js -->
<script src="{{asset('assets/js/pages/toaster.init.js')}}"></script>
<!-- jstree js -->
<script src="{{asset('assets/libs/jstree/jstree.min.js')}}"></script>

<!-- treeview -->
<script src="{{asset('assets/js/pages/treeview.init.js')}}"></script>


<!-- apexcharts init -->
{{-- <script src="{{asset('assets/js/pages/widgets.init.js')}}"></script> --}}
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<!-- <script src="https://cdn.tiny.cloud/1/qagffr3pkuv17a8on1afax661irst1hbr4e6tbv888sz91jc/tinymce/6/tinymce.min.js"></script> -->
@if(!request()->routeIs('monthly.sheet'))
 <!-- Vendors JS -->
 {{-- <script src="https://demos.pixinvent.com/frest-html-admin-template/assets/vendor/libs/quill/katex.js"></script>
<script src="https://demos.pixinvent.com/frest-html-admin-template/assets/vendor/libs/quill/quill.js"></script> --}}
<script src="https://demos.pixinvent.com/frest-html-admin-template/assets/js/forms-editors.js"></script>
@endif
<script>
    "use strict"; $(function () { if(typeof autosize !== 'undefined') { autosize($(".autosize")) } });
</script>
<!-- bs custom file input plugin -->
<script src="{{asset('assets/libs/clipboard/clipboard.min.js')}}"></script>
<script>
    "use strict";$(function(){new ClipboardJS(".btn")});
</script>


<!-- init js -->
<!-- <script>
    if ($("#basic-example").length > 0) {
        tinymce.init({
            selector: 'textarea#basic-example',
            height: 400,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });
    }
</script> -->

<script>
    $(document).ready(function(){
  $(".dropify").dropify();
});
</script>


</body>
</html>