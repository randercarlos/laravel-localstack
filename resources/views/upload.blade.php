<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>AlgaWorks</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendors.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/algaworks.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/application.css') }}"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="aw-layout-simple-page">
<div class="aw-layout-simple-page__container">

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br />
            @endforeach
        </div>
    @endif

    <div class="alert alert-success" id="alert-success" style="display: none">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        File was upload successfuly.
    </div>

    @if (session('info'))
        <div class="alert alert-info" id="alert-info">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {!! session('info') !!}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" id="alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {!! session('error') !!}
        </div>
    @endif
    <form action="/upload" method="post" enctype="multipart/form-data" id="uploadForm">
        @csrf
        <div class="aw-simple-panel">
            <div class="aw-simple-panel__box">
                <div class="form-group">
                    <button type="button" class="btn  btn-primary btn-lg aw-btn-full-width upload-btn">Selecione um arquivo para upload.</button>
                    <input type="file" id="fileInput" name="file" style="display: none;">
                </div>

                <div class="table-responsive">
                    <table id="tabela-produtos" class="table  table-striped  table-bordered  table-hover  table-condensed  js-sticky-table">
                        <thead class="aw-table-header-solid">
                        <tr>
                            <th >Nome</th>
                            <th style="width: 70px; text-align: center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($savedUploadsUrls as $savedUploadsUrl)
                        <tr >
                            <td><a href="{{ $savedUploadsUrl->url }}" target="_blank" download>{{ $savedUploadsUrl->name }}</a></td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-default btn-xs alert-danger" href="{{ route('deleteUpload', ['encryptedFilename' => $savedUploadsUrl->hash]) }}">
                                        x
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('js/vendors.min.js') }}"></script>
<script src="{{ asset('js/algaworks.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the upload button element
        var uploadBtn = document.querySelector('.upload-btn');

        // Get the file input element
        var fileInput = document.getElementById('fileInput');

        // Add click event listener to the upload button
        uploadBtn.addEventListener('click', function() {
            // Trigger click event on the file input
            fileInput.click();
        });

        // Add change event listener to the file input
        fileInput.addEventListener('change', function() {
            // Submit the form when a file is selected
            document.getElementById('uploadForm').submit();
        });
    });
</script>
</body>
</html>
