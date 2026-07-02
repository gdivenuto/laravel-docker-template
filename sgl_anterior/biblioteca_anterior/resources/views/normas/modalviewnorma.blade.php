<div id="modalViewNorma" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Norma</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
				        <div class="col-md-6">
				        	<p>
				                <strong class="text-uppercase">Nro. Interno:</strong>
				                <span id="nro_hcd"></span>
				            </p>
				        </div>
				        <div class="col-md-6">
				        	<p>
				        		<strong class="text-uppercase">Expediente H.C.D.: </strong>
				        		<span id="hcd_expedientes"></span>
				        		<span id="bloque"></span>
				        	</p>
				        </div>
				        
				        <div class="w-100"></div>

				        <div class="col-md-6">
				        	<p>
				                <strong class="text-uppercase">Acto:</strong> 
				                <span id="acto_desc"></span>
				                <span id="nro"></span>
				                [
				                    <a id="url_html" class="link-norma-doc" href="#" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Texto</a>
				                    |
				                    <a id="url_pdf" class="link-norma-doc" href="#" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a>
				                ] 
				                <br/>
				                [
				                    <a id="url_actualizado_html" class="link-norma-doc" href="#" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Actualizado </a>
				                    |
				                    <a id="url_actualizado_pdf" class="link-norma-doc" href="#" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a>
				                ]
				            </p>
				        </div>

				        <div class="col-md-6">
				        	<p>
				                <strong class="text-uppercase">Expediente D.E.:</strong> 
				                <span id="exped"></span>
				            </p>
				        </div>

				        <div class="w-100"></div>

				        <div class="col-md-4">
				        	<p>
				                <strong class="text-uppercase">Sanci&oacute;n:</strong> 
				                <span id="fec_sancion"></span>
				            </p>
				        </div>
				        <div class="col-md-4">
				        	<p>
				                <strong class="text-uppercase">Promulgaci&oacute;n:</strong> 
				                <span id="fec_promulga"></span>
				            </p>
				        </div>
				        <div class="col-md-4">
				        	<p>
				                <strong class="text-uppercase">Publicaci&oacute;n:</strong> 
				                <span id="fec_publica"></span>
				            </p>
				        </div>

<!--
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
				                    [
				                        <a class="link-norma-doc" href="{{ $norma->url_abrogacion_html }}" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Texto</a>
				                        |
				                        <a class="link-norma-doc" href="{{ $norma->url_abrogacion_pdf }}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a>
				                    ]
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

						@if ($norma->relaciones->count())
						<div class="w-100"></div>
				        <div class="col-md-12">
				        	<ul>
				        		@foreach ($norma->relaciones as $r)
				        			<li>
				        				<strong class="text-uppercase">{{ $r->relacion_desc }}:</strong> 
				                        @if (!empty($r->a) && !empty($r->n))
				                            <a href="{{ route('normas.showbyactonumero', [ 'normas_db' => $normas_db, 'acto' => $r->a, 'nro' => $r->n ]) }}">
				                                <i class="fa fa-link" aria-hidden="true"></i> 
				                                {{ $r->acto_desc }} {{ $r->n }}
				                            </a>
				                        @else
				                            {{ $r->acto_desc }} {{ $r->n }} 
				                        @endif
				                        {{ $r->p }}
				        				[
				        				<a class="link-norma-doc" href="{{ $r->url_html }}" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> Texto</a> | 
				        				<a class="link-norma-doc" href="{{ $r->url_pdf }}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a>
				        				]
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

				    	<div class="col-md-2"><p><strong class="text-uppercase">Alcance Normativo:</strong> {{ $norma->alcance_desc }} </p></div>
				    	<div class="col-md-2"><p><strong class="text-uppercase">Car&aacute;cter:</strong> {{ $norma->caracter_desc }} </p></div>
				    	<div class="col-md-2"><p><strong class="text-uppercase">Recopilación:</strong> {{ $norma->recopila_desc }} </p></div>

				        <div class="w-100"></div>

				        @if (! empty($norma->sin_nro))
				    	   <div class="col-md-2"><p><strong class="text-uppercase">Sin N°:</strong> {{ $norma->sin_nro }} </p></div>
				        @endif
				    	@if (! empty($norma->fec_incluido))
				            <div class="col-md-2">
				                <p>
				                    <strong class="text-uppercase">Inclu&iacute;do:</strong> 
				                    {{ \Carbon\Carbon::parse($norma->fec_incluido)->format('d/m/Y') }}
				                </p>
				            </div>
				        @endif
				        @if (! empty($norma->fec_excluido))
				        	<div class="col-md-2">
				                <p>
				                    <strong class="text-uppercase">Exclu&iacute;do:</strong> 
				                    {{ \Carbon\Carbon::parse($norma->fec_excluido)->format('d/m/Y') }}
				                </p>
				            </div>
				        @endif

				        <div class="w-100"></div>

				        @if (! empty($norma->ingresa))
				    	   <div class="col-md-2"><p><strong class="text-uppercase">Ingresa:</strong> {{ $norma->ingresa }} </p></div>
				        @endif
				    	<div class="col-md-2"><p><strong class="text-uppercase">Aprobado:</strong> {{ $norma->aprobado_desc }} </p></div>
				    	<div class="col-md-2"><p><strong class="text-uppercase">Ausentes:</strong> {{ $norma->ausentes }} </p></div>

				        <div class="w-100"></div>

				        <div class="col-md-2"><p><strong class="text-uppercase">N° Tema:</strong> {{ $norma->nro_tema }} </p></div>
				    	@if ($norma->procesamientos->count() > 0)
				    	<div class="col-md-12">
				    		<p>
				    			<strong class="text-uppercase">Procesamientos:</strong> {{ join(', ', $norma->procesamientos->pluck('nombre')->toArray()) }}
				    		</p>
				    	</div>
				        @endif

				        @if ($norma->abstenciones->count() > 0)
				    	<div class="col-md-12">
				    		<p>
				    			<strong class="text-uppercase">Abstenciones:</strong> {{ join(', ', $norma->abstenciones->pluck('nombre')->toArray()) }}
				    		</p>
				    	</div>
				        @endif
-->	
				    </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>