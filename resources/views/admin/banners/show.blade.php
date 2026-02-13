@extends('admin.layouts.app')

@section('title', 'Detalle del Banner')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Detalle del Banner</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            {{-- Imagen del Banner --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Imagen</h5>
                    <span class="badge bg-{{ $banner->status == 'active' ? 'success' : 'secondary' }}">
                    {{ $banner->status == 'active' ? 'Activo' : 'Inactivo' }}
                </span>
                </div>
                <div class="card-body p-0">
                    <div class="banner-preview">
                        <img src="{{ Storage::url($banner->url) }}" alt="{{ $banner->alt_text }}" class="img-fluid w-100">
                    </div>
                </div>
            </div>

            {{-- Enlace --}}
            @if($banner->link_url)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-link me-2"></i>Enlace Asociado</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <a href="{{ $banner->link_url }}" target="_blank" class="text-break">
                                    {{ $banner->link_url }}
                                </a>
                            </div>
                            <div>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-{{ $banner->link_target == '_blank' ? 'external-link-alt' : 'arrow-right' }} me-1"></i>
                            {{ $banner->link_target == '_blank' ? 'Nueva ventana' : 'Misma ventana' }}
                        </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            {{-- Informacion del Archivo --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informacion del Archivo</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Nombre original:</td>
                            <td class="text-end text-break">{{ $banner->original_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dimensiones:</td>
                            <td class="text-end">{{ $banner->dimensions ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tamano:</td>
                            <td class="text-end">{{ $banner->formatted_file_size }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipo:</td>
                            <td class="text-end">{{ $banner->mime_type }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Orden:</td>
                            <td class="text-end">#{{ $banner->order }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Texto Alternativo --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-universal-access me-2"></i>Accesibilidad</h5>
                </div>
                <div class="card-body">
                    <label class="form-label text-muted small">Texto alternativo (Alt)</label>
                    <p class="mb-0">{{ $banner->alt_text ?: 'Sin texto alternativo' }}</p>
                </div>
            </div>

            {{-- Fechas --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Fechas</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Creado:</td>
                            <td class="text-end">{{ $banner->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Actualizado:</td>
                            <td class="text-end">{{ $banner->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Zona de Peligro</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Esta accion no se puede deshacer. Se eliminara permanentemente el banner.</p>
                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" data-confirm="Estas seguro de eliminar este banner? Esta accion no se puede deshacer.">
                            <i class="fas fa-trash me-2"></i>Eliminar Banner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .banner-preview {
                background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%),
                linear-gradient(-45deg, #f0f0f0 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #f0f0f0 75%),
                linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
                background-size: 20px 20px;
                background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            }
            .banner-preview img {
                display: block;
            }
        </style>
    @endpush
@endsection<?php
