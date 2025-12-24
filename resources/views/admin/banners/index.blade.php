@extends('admin.layouts.app')

@section('title', 'Banners')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Banners del Carrusel</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Banners</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Banner
        </a>
    </div>
@endsection

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.banners.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o descripción..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Banners Grid --}}
<div class="card">
    <div class="card-body">
        @if($banners->count() > 0)
            <div class="row" id="banners-sortable" data-sortable="{{ route('admin.banners.reorder') }}">
                @foreach($banners as $banner)
                    <div class="col-md-4 col-lg-3 mb-4" data-id="{{ $banner->id }}">
                        <div class="card h-100 banner-card">
                            <div class="banner-image-wrapper position-relative">
                                <img src="{{ Storage::url($banner->url) }}" class="card-img-top" alt="{{ $banner->alt_text }}">
                                <div class="banner-overlay">
                                    <i class="fas fa-grip-lines drag-handle" style="cursor: grab;"></i>
                                </div>
                                <span class="badge badge-status-{{ $banner->status }} position-absolute" style="top: 10px; right: 10px;">
                                    {{ ucfirst($banner->status) }}
                                </span>
                            </div>
                            <div class="card-body p-2">
                                <div class="small text-muted mb-1">
                                    @if($banner->alt_text)
                                        {{ Str::limit($banner->alt_text, 30) }}
                                    @else
                                        {{ $banner->original_name }}
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        {{ $banner->dimensions }} &bull; {{ $banner->formatted_file_size }}
                                    </small>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-outline-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar" data-confirm="¿Eliminar este banner?">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($banner->link_url)
                                    <div class="small mt-1">
                                        <i class="fas fa-link text-muted me-1"></i>
                                        <a href="{{ $banner->link_url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;">
                                            {{ $banner->link_url }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($banners->hasPages())
                <div class="mt-3">
                    {{ $banners->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h5>No hay banners</h5>
                <p>Sube imágenes para el carrusel de banners de tu empresa.</p>
                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Banner
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .banner-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .banner-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .banner-image-wrapper {
        height: 150px;
        overflow: hidden;
        background: #f8f9fa;
    }
    .banner-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .banner-overlay {
        position: absolute;
        top: 10px;
        left: 10px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .banner-card:hover .banner-overlay {
        opacity: 1;
    }
    .banner-overlay i {
        background: rgba(255,255,255,0.9);
        padding: 5px 10px;
        border-radius: 4px;
    }
</style>
@endpush
@endsection
