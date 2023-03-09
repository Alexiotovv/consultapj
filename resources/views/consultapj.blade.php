@extends('layouts.base')
@section('contenido')
    <div class="card">
        <div class="alert alert-info" role="alert" style="text-align: center; margin: 15px;">
            Búsqueda de Proyectos por código
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header" style="text-align: -webkit-center;">
                            <h5>Código de Proyecto</h5>
                        </div>
                        <div class="card-body">
                            

                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="">Periodo</label>
                                    <select id="slPeriodo" class="form-select">
                                        <option value="2023">2023</option>
                                        <option value="2022">2022</option>
                                    </select>
                                    <div class="" id="spinner" role="status" style="position: fixed">
                                        
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <label for="">Ingrese Código</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="txtCodigo" style="100%;">
                                        <button class="btn btn-primary" id="btnBuscar"><i class="bi-search"></i> Buscar</button>
                                    </div>
                                </div>

                                
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="">Nombre Inversión:</label>
                                    <textarea type="text" id="txtNombreInversion" class="form-control" style="height: 130px;" disabled></textarea>
                                    <label for="">Tipo Inversión:</label>
                                    <input type="text" id="txtTipoInversion" class="form-control" disabled>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6" style="text-align: -webkit-center;">
                    <div class="card" style="min-height: 385px;">
                        <div class="card-header">
                            <h5>Código QR Generado</h5>
                        </div>
                        <div class="card-body">
                            <div id="CodigoQR">
                                {{-- Aqui va el código QR --}}
                            </div>
                            <label for="" id="EtiquetaQR"></label>
                            <a id="btnDescargar" download="" class="btn btn-primary" style="width: 50%;" hidden><i class="bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
  
    <!-- Modal -->
    <div class="modal fade" id="modalMensaje" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Mensaje</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="msjeContenido"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal -->
    

    <script src="../js/jquery.js"></script>
    <script src="../js/jquery-qrcode-0.18.0.js"></script>
    <script src="../js/jquery-qrcode-0.18.0.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script>
        datos = {};
        var source = "";
        ObtenerDatos("2023");
        $("#slPeriodo").on('change', function() {
            ObtenerDatos($("#slPeriodo").val());
            LimpiarCajas();
        });
        
        $("#btnDescargar").on("click",function (e) {
            e.preventDefault();
           
            var images = $('#CodigoQR img').prop('src');
            var source = images;
            var a = document.createElement('a');
            a.download = "QRCode_"+$("#txtCodigo").val();
            a.target = '_blank';
            a.href= source;
            a.click();
        });

        $("#btnBuscar").on('click', function(e) {
            e.preventDefault();
            
            var cod_find='0';
            $("#CodigoQR").html("");
            $("#EtiquetaQR").text('');
            if ($("#txtCodigo").val() !== "") {
                datos.forEach(element => {
                    if (element['codigo'] == $("#txtCodigo").val()) {
                        cod_find='1';
                        $("#txtNombreInversion").val(element['nombredeinversion']);
                        $("#txtTipoInversion").val(element['tipoinversion']);
                        url = "https://ofi5.mef.gob.pe/inviertews/Repseguim/ResumF12B?codigo=" + $(
                            "#txtCodigo").val();
                        
                        $("#CodigoQR").qrcode({
                            render: 'image',
                            mode: 2,
                            label: "",
                            width: 150,
                            height: 150,
                            size: 250,
                            radius: 0.5,
                            color: '#000',
                            text: url,
                        });
                        $("#EtiquetaQR").text(url);
                        $("#btnDescargar").attr('hidden',false);
                    }
                });

                if (cod_find=='0') {
                    $("#msjeContenido").text('El código ingresado no existe en el periodo');
                    $("#modalMensaje").modal('show');
                }

            } else {
                alert("Ingrese un código");
            }


        });
        $("#txtCodigo").on("keyup", function() {
            LimpiarCajas();
        });
        function ObtenerDatos(ano) {
            datos = {};
            $.ajax({
                type: "GET",
                url: "https://aplicaciones.regionloreto.gob.pe/aplicaciones/v2/Apirest/" + ano,
                dataType: "json",
                beforeSend: function() {
                    $("#spinner").addClass("spinner-border");
                },
                success: function(response) {
                    datos = response;
                    $("#spinner").removeClass("spinner-border");
                }
            });
        }
        function LimpiarCajas() {
            $("#txtNombreInversion").val('');
            $("#txtTipoInversion").val('');
            $("#CodigoQR").html("");
            $("#EtiquetaQR").text('');
            $("#btnDescargar").attr('hidden',true);
        }
    </script>

@endsection
