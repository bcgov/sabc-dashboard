<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }
    </style>
    {!! $style !!}
</head>
<body>

{!! $html !!}
<htmlpagefooter name="page-footer" class="htmlpagefooter-pdf">
    <div style="font-size:4px !important; color: #eeeeee !important;">{PAGENO}/{nbpg}</div>
</htmlpagefooter>
</body>
</html>
