@extends('admin.layouts.app')

@section('title', 'Noticias')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Cintillo de Noticias</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Noticias</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Noticia
        </a>
    </div>
@endsection

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.news.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar en el texto..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select">
                    <option value="">Prioridad</option>
                    <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>Prioritarias</option>
                    <option value="0" {{ request('priority') == '0' ? 'selected' : '' }}>Normales</option>
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

{{-- News Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="40"></th>
                        <th>Contenido</th>
                        <th>Enlace</th>
                        <th>Vigencia</th>
                        <th>Prioridad</th>
                        <th>Creado por</th>
                        <th>Estado</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                        <tr data-id="{{ $item->id }}">
                            <td>
                                <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
                            </td>
                            <td>
                                <div>{{ Str::limit($item->content, 80) }}</div>
                                @if(!$item->isCurrentlyActive())
                                    <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>No visible actualmente</small>
                                @endif
                            </td>
                            <td>
                                @if($item->url)
                                    <a href="{{ $item->url }}" target="_blank" class="small text-truncate d-inline-block" style="max-width: 150px;">
                                        {{ $item->url }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($item->published_at || $item->expires_at)
                                    <div>
                                        @if($item->published_at)
                                            <span class="text-muted">Desde:</span> {{ $item->published_at->format('d/m/Y H:i') }}
                                        @endif
                                    </div>
                                    <div>
                                        @if($item->expires_at)
                                            <span class="text-muted">Hasta:</span> {{ $item->expires_at->format('d/m/Y H:i') }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">Sin límite</span>
                                @endif
                            </td>
                            <td>
                                @if($item->priority > 0)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i>Prioritaria</span>
                                @else
                                    <span class="text-muted">Normal</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($item->creator)
                                    <div>{{ $item->creator->name }}</div>
                                    <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <label class="status-toggle" data-toggle-url="{{ route('admin.news.toggle-status', $item) }}" title="{{ $item->status == 'active' ? 'Clic para desactivar' : 'Clic para activar' }}">
                                    <input type="checkbox" {{ $item->status == 'active' ? 'checked' : '' }}>
                                    <span class="toggle-switch"></span>
                                </label>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" data-confirm="¿Eliminar esta noticia?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-newspaper"></i>
                                    <h5>No hay noticias</h5>
                                    <p>Crea noticias para mostrar en el cintillo de tu empresa.</p>
                                    <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nueva Noticia
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($news->hasPages())
        <div class="card-footer">
            {{ $news->links() }}
        </div>
    @endif
</div>
@endsection
