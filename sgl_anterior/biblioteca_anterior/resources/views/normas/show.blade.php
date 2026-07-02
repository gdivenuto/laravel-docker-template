@extends('layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // Delegates --------------------------------------------------------------
    var disableInvalidLinks = function () {
        $('a.link-norma-doc').each(function () {
            var link_obj = $(this);
            var doc_url = link_obj.attr('href');
            $.ajax({
                method: 'HEAD',
                url: doc_url,
                // custom attribute
                link_instance: link_obj
            })
            .fail(function(jqXHR, textStatus, errorThrown ) {
                var contents = this.link_instance.contents();
                contents.unwrap();
                //contents.wrap('<del></del>');
                contents.wrap('<span style="display:none"></span>');
            });
        });
    };

    @include('normas.devicedetect')

    // jQuery Document Ready --------------------------------------------------
    $(function () {
        // Device detection
        if (detectDevice().device == 'computer')
            $('.mobile-only').hide();
        else
            $('a').removeAttr('target');

        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

        // Back buttons
        $('a.mobile-only').click(function (e) {
            e.preventDefault();
            window.history.back();
        });

        // Update links
        disableInvalidLinks();
    });
</script>
@endsection

@section('content')
<div class="container-fluid">
    @auth
        <div class="row mt-0 mb-2">
            <div class="col-12 col-md-6">
                <a  class="btn btn-secondary" role="button" 
                    href="{{ route('backend.normas.index') }}">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
                </a>
                <a href="{{ route('backend.normas.edit', ['norma' => $norma->id])}}" class="btn btn-primary" role="button"><i class="fa fa-pencil" aria-hidden="true"></i> Editar</a>
                <a href="{{ route('backend.normas.clonar', ['norma' => $norma->id])}}" class="btn btn-secondary" role="button"><i class="fa fa-clone" aria-hidden="true"></i> Duplicar</a>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0">
                @if ($norma_ordenanza_anterior != null && isset($norma_ordenanza_anterior->nro))
                    <a  class="btn btn-secondary" role="button" 
                        href="{{ route('normas.show', [ 'normas_db' => $normas_db, 'norma' => intval($norma_ordenanza_anterior->id)]) }}">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> 
                        Ver Ordenanza anterior
                    </a>
                @endif
                @if ($norma_ordenanza_siguiente != null && isset($norma_ordenanza_siguiente->nro))
                    <a  class="btn btn-secondary" role="button" 
                        href="{{ route('normas.show', [ 'normas_db' => $normas_db, 'norma' => intval($norma_ordenanza_siguiente->id)]) }}">
                        Ver siguiente Ordenanza
                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </div>
    @endauth

    <h2 class="text-capitalize">{{ $normas_db }}</h2>
    
    <div class="row">
        <div class="col-12">
            <a href="#" class="btn btn-secondary mobile-only float-right" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Nro. Interno:</strong> 
                        {{ (!empty($norma->nro_hcd)) ? $norma->nro_hcd : '(no posee)'}}
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Expediente H.C.D.: </strong>

                        @guest
                            {{ (!empty($norma->hcd_exped)) ? $norma->hcd_exped : '(no posee)' }}
                        @endguest
                        
                        @auth
                            @if (!empty($norma->hcd_exped)) 
                                <a href="#" data-toggle="modal" data-target="#modalDocumentos">
                                    {{ $norma->hcd_exped }}
                                </a> 
                            @else
                                (no posee)
                            @endif
                        @endauth

                        @if (! empty($norma->bloque))
                            <strong class="text-uppercase ml-3">D&iacute;gito:</strong> {{ Str::upper($norma->bloque) }}
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('normas.showpdf', [ 'normas_db' => $normas_db, 'norma' => $norma ]) }}" class="btn btn-sm btn-secondary" role="button">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;Ficha
                    </a>
                    @auth
                        &nbsp;
                        <a href="{{ route('normas.showtransporte', [ 'normas_db' => $normas_db, 'norma' => $norma ]) }}" class="btn btn-sm btn-secondary" role="button" title="Formato TRANSP (winisis), para copiar el texto.">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;Transporte
                        </a>
                    @endauth
                </div>

                <div class="w-100"></div>

                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Acto:</strong> {{ $norma->acto_desc }} {{ $norma->nro }}
                            <a class="link-norma-doc" href="{{ $norma->url_html }}" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Texto</a>
                            &nbsp;&nbsp;
                            <a class="link-norma-doc" href="{{ $norma->url_pdf }}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a>
                            &nbsp;&nbsp;
                            <a class="link-norma-doc" href="{{ $norma->url_actualizado_html }}" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Actualizado </a>
                            &nbsp;&nbsp;
                            <a class="link-norma-doc" href="{{ $norma->url_actualizado_pdf }}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF Actualizado</a>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Expediente D.E.:</strong> 
                        {{ (!empty($norma->exped)) ? $norma->exped : '(no posee)' }}
                    </p>
                </div>

                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Sanci&oacute;n:</strong> 
                        @if (!is_null($norma->fec_sancion))
                            {{ \Carbon\Carbon::parse($norma->fec_sancion)->format('d/m/Y') }}
                        @else
                            (sin fecha)
                        @endif
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Promulgaci&oacute;n:</strong> 
                        @if (!is_null($norma->fec_promulga))
                            {{ \Carbon\Carbon::parse($norma->fec_promulga)->format('d/m/Y') }}
                        @else
                            (sin fecha)
                        @endif
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Publicaci&oacute;n:</strong> 
                        @if (!is_null($norma->fec_publica))
                            {{ \Carbon\Carbon::parse($norma->fec_publica)->format('d/m/Y') }}
                        @else
                            (sin fecha)
                        @endif
                    </p>
                </div>

                @if (!empty($norma->boletin_nro) || !empty($norma->boletin_pag))
                    <div class="w-100"></div>
                    <div class="col-md-6">
                        <p>
                            <strong class="text-uppercase">Bolet&iacute;n N°:</strong> 
                            {{ (!empty($norma->boletin_nro) && $norma->boletin_nro != 'null') ? $norma->boletin_nro : '(sin número)' }} 
                            <strong class="text-uppercase"> - Pág.:</strong> 
                            {{ (!empty($norma->boletin_pag) && $norma->boletin_pag != 'null') ? $norma->boletin_pag : '(sin página)' }}
                        </p>
                    </div>
                @endif

                @if (!empty($norma->registro_t) || !empty($norma->registro_f))
                    <div class="w-100"></div>
                    <div class="col-md-6">
                        <p>
                            <strong class="text-uppercase"> - Registrado</strong> 
                            @if (!empty($norma->registro_t))
                                <strong class="text-uppercase">Tomo</strong> {{ $norma->registro_t }}
                            @endif
                            @if (!empty($norma->registro_f))
                                <strong class="text-uppercase">Folio</strong> {{ $norma->registro_f }}
                            @endif
                        </p>
                    </div>
                @endif

                @if (!empty($norma->abrogacion_a) || !empty($norma->abrogacion_n))
                    <div class="w-100"></div>
                    <div class="col-md-6">
                        <p>
                            <strong class="text-uppercase">Abrogaci&oacute;n:</strong> 
                            <a href="{{ route('normas.showbyactonumero', [ 'normas_db' => $normas_db, 'acto' => $norma->abrogacion_a, 'nro' => $norma->abrogacion_n ]) }}">
                                <i class="fa fa-link" aria-hidden="true"></i> 
                                {{ $norma->abrogacion_a_desc }} {{ $norma->abrogacion_n }}
                            </a>
                                <a class="link-norma-doc" href="{{ $norma->url_abrogacion_html }}" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Texto</a>
                                &nbsp;&nbsp;
                                <a class="link-norma-doc" href="{{ $norma->url_abrogacion_pdf }}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a>
                        </p>
                    </div>
                @endif

                @if (! empty($norma->dec_promulga))
                    <div class="w-100"></div>
                    <div class="col-md-6">
                        <p><strong class="text-uppercase">Decreto Promulgaci&oacute;n N°:</strong> {{ $norma->dec_promulga }} </p>
                    </div>
                @endif

                <div class="w-100"></div>
                <div class="col-md-12">
                    <p><strong class="text-uppercase">Contenido:</strong> {{ $norma->contenido }}</p>
                </div>

                <div class="w-100"></div>
                <div class="col-md-12">
                    <p>
                        <strong class="text-uppercase">Descriptores:</strong>
                        @forelse ($norma->descriptores as $d)
                            <a href="{{ route('normas.searchsimple', [ 'normas_db' => $normas_db, 'descriptor_id' => $d->id ]) }}" class="badge badge-secondary" target="_blank">{{ $d->tag }}</a>
                        @empty
                            - no hay descriptores definidos para esta norma -
                        @endforelse
                    </p>
                </div>

                @if ($norma->relaciones->count() > 0)
                <div class="w-100"></div>
                <div class="col-md-12">
                    <ul>
                        @foreach ($norma->relaciones as $r)
                            <li>
                                <strong class="text-uppercase">{{ $r->relacion_desc }}:</strong> 
                                @php
                                    $nr = $r->relNormaByActoNro();
                                @endphp
                                @if ($nr)
                                    <a href="{{ route('normas.show', [ 'normas_db' => $normas_db, 'norma' => $nr->id ]) }}">
                                        <i class="fa fa-link" aria-hidden="true"></i> 
                                        {{ $nr->acto_desc }} {{ $nr->nro }}
                                    </a>
                                    {{ $r->p }}
                                    @if ($nr->url_html != '')
                                        <a class="link-norma-doc" href="{{ $nr->url_html }}" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Texto</a>
                                    @endif
                                    &nbsp;&nbsp;
                                    @if ($nr->url_pdf != '')
                                        <a class="link-norma-doc" href="{{ $nr->url_pdf }}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a>
                                    @endif
                                @else
                                    {{ $r->acto_desc }} {{ $r->n }} 
                                    {{ $r->p }}
                                    @if ($r->url_html != '')
                                        <a class="link-norma-doc" href="{{ $r->url_html }}" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Texto</a>
                                    @endif
                                    &nbsp;&nbsp;
                                    @if ($r->url_pdf != '')
                                        <a class="link-norma-doc" href="{{ $r->url_pdf }}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a>
                                    @endif
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="w-100"></div>
                <div class="col-md-12">
                    <p>
                        <strong class="text-uppercase">Observaciones:</strong>
                        <ul>
                        @forelse ($norma->observaciones as $o)
                            <li>{{ $o->obs }}</li>
                        @empty
                            <li>No hay observaciones para esta norma.</li>
                        @endforelse
                        </ul>
                    </p>
                </div>

                <div class="w-100"></div>

                <div class="col-md-4"><p><strong class="text-uppercase">Alcance Normativo:</strong> {{ $norma->alcance_desc }} </p></div>
                <div class="col-md-4"><p><strong class="text-uppercase">Car&aacute;cter:</strong> {{ $norma->caracter_desc }} </p></div>
                <div class="col-md-4"><p><strong class="text-uppercase">Recopilación:</strong> {{ $norma->recopila_desc }} </p></div>
            </div>
        </div>

        @auth
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <hr>
                    <h4>Informaci&oacute;n adicional</h4>
                </div>
                
                <div class="col-md-4"><p><strong class="text-uppercase">Sin N°:</strong> {{ (!empty($norma->sin_nro)) ? $norma->sin_nro : '(no posee)'}} </p></div>
                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Fecha Inclu&iacute;do:</strong> 
                        {{ (!empty($norma->fec_incluido)) ? \Carbon\Carbon::parse($norma->fec_incluido)->format('d/m/Y') : '(no posee)'}}
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Fecha Exclu&iacute;do:</strong> 
                        {{ (!empty($norma->fec_excluido)) ? \Carbon\Carbon::parse($norma->fec_excluido)->format('d/m/Y') : '(no posee)'}}
                    </p>
                </div>

                <div class="w-100"></div>

                <div class="col-md-4"><p><strong class="text-uppercase">Ingresa:</strong> {{ (!empty($norma->ingresa)) ? $norma->ingresa : '(no posee)'}} </p></div>
                <div class="col-md-4"><p><strong class="text-uppercase">Aprobado:</strong> {{ $norma->aprobado_desc }} </p></div>
                <div class="col-md-4"><p><strong class="text-uppercase">Ausentes:</strong> {{ $norma->ausentes }} </p></div>

                <div class="w-100"></div>

                <div class="col-md-4"><p><strong class="text-uppercase">N&deg; Tema:</strong> {{ $norma->nro_tema }} </p></div>
                
                <?php /**
                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Procesamientos:</strong>
                        {{ (!empty($norma->exped)) ? $norma->exped : '(no posee)' }}
                        {{ ($norma->procesamientos->count() > 0) ? join(', ', $norma->procesamientos->pluck('nombre')->toArray()) : '(ninguno)' }}
                    </p>
                </div>
                <?php /**/ ?>

                <div class="col-md-4">
                    <p>
                        <strong class="text-uppercase">Abstenciones:</strong> {{ ($norma->abstenciones->count() > 0) ? join(', ', $norma->abstenciones->pluck('nombre')->toArray()) : '(no posee)'}}
                    </p>
                </div>

                <div class="w-100"></div>

                <div class="col-md-12">
                    <strong>ACTAS:</strong><br/>
                    <ul>
                        @forelse ($norma->actas as $a)
                            <li>
                                Acta N&deg;: {{ (!empty($a->acta_n)) ? $a->acta_n : '-' }}, Reuni&oacute;n: {{ (!empty($a->acta_r)) ? $a->acta_r : '-' }}, Tipo: {{ (!empty($a->acta_t)) ? $a->tipo_nombre : '-' }}
                            </li>
                        @empty
                            <li>No posee actas.</li>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
        @endauth
     
{{--
        <div class="col-md-2">
            <div class="row">
                <div class="col-md-12 text-center">
                    <img class="img-fluid" src="{{ route('qr.norma', [ 'normas_db' => $normas_db, 'norma' => $norma ]) }}"></img>
                </div>
                <div class="col-md-12">
                    <a href="{{ route('normas.showpdf', [ 'normas_db' => $normas_db, 'norma' => $norma ]) }}" class="btn btn-sm btn-secondary btn-block" role="button" >
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Descargar PDF
                    </a>
                </div>
            </div>
        </div>
--}}
    </div>
    <div class="row">
        <div class="col-12">
            <a href="#" class="btn btn-secondary mobile-only float-right" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> Volver</a>
        </div>
    </div>
</div>

@auth
<!-- Modal para visualizar los documentos del Expediente del HCD (si tiene asignado uno) -->
<div class="modal fade" id="modalDocumentos" tabindex="-1" aria-labelledby="modalDocumentosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDocumentosLabel">
                    Documentos del Expediente del HCD.
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ( isset($documentos[1]) && count($documentos[1]) > 0)
                    <ul>
                        @foreach ($documentos[1] as $file)
                            <li>
                                <a href="{{ $documentos[0].$file }}" target="_blank">{{ $file }}</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-primary" role="alert">El Expediente del HCD no posee documentos.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endauth

@endsection