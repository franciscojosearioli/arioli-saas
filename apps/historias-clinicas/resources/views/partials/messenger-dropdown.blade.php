@php
    $_msgUnread  = \App\Models\QaTopic::unreadCount();
    $_msgTopics  = \App\Models\QaTopic::recentForNavbar(5);
    $_isAdmin    = auth()->user()->is_admin;
    $_msgIndex   = $_isAdmin ? route('admin.messenger.index')  : route('panel.messenger.index');
    $_msgCreate  = $_isAdmin ? route('admin.messenger.createTopic') : route('panel.messenger.createTopic');
    $_msgShow    = fn($id) => $_isAdmin ? route('admin.messenger.showMessages', $id) : route('panel.messenger.showMessages', $id);
@endphp

<li class="c-header-nav-item dropdown">
    <a href="#" class="c-header-nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
       title="Mensajería">
        <i class="far fa-envelope"></i>
        @if($_msgUnread > 0)
            <span class="badge badge-pill badge-danger navbar-badge">{{ $_msgUnread }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-right" style="min-width:320px; max-width:360px;">

        {{-- Header --}}
        <div class="dropdown-header d-flex justify-content-between align-items-center bg-light py-2 px-3">
            <span class="font-weight-bold">
                <i class="fas fa-envelope mr-1 text-primary"></i> Mensajes
                @if($_msgUnread > 0)
                    <span class="badge badge-danger ml-1">{{ $_msgUnread }}</span>
                @endif
            </span>
            <a href="{{ $_msgCreate }}" class="btn btn-xs btn-success">
                <i class="fas fa-plus"></i> Nuevo
            </a>
        </div>

        {{-- Lista de conversaciones recientes --}}
        @forelse($_msgTopics as $_t)
            @php($_other = $_t->receiverOrCreator())
            <a href="{{ $_msgShow($_t->id) }}"
               class="dropdown-item py-2 {{ $_t->unread_count > 0 ? 'font-weight-bold' : '' }}"
               style="border-bottom: 1px solid #f0f0f0; white-space:normal;">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 mr-2">
                        <div class="rounded-circle {{ $_t->unread_count > 0 ? 'bg-primary' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center"
                             style="width:30px;height:30px;font-size:.75em;font-weight:700;">
                            {{ $_other ? strtoupper(substr($_other->name ?? $_other->email, 0, 1)) : '?' }}
                        </div>
                    </div>
                    <div class="flex-grow-1" style="min-width:0">
                        <div class="d-flex justify-content-between">
                            <span class="small text-truncate" style="max-width:180px;">
                                {{ $_other ? ($_other->name ?? $_other->email) : '(eliminado)' }}
                            </span>
                            <small class="text-muted ml-1 flex-shrink-0" style="font-size:.7em;">
                                {{ $_t->created_at->diffForHumans(null, true, true) }}
                            </small>
                        </div>
                        <div class="text-truncate small {{ $_t->unread_count > 0 ? 'text-dark' : 'text-muted' }}"
                             style="max-width:260px;">
                            {{ $_t->subject }}
                        </div>
                    </div>
                    @if($_t->unread_count > 0)
                        <span class="badge badge-danger badge-pill ml-2 flex-shrink-0">{{ $_t->unread_count }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="dropdown-item text-center text-muted py-3 small">
                <i class="fas fa-inbox d-block mb-1 fa-lg"></i>
                Sin mensajes
            </div>
        @endforelse

        {{-- Footer --}}
        <div class="dropdown-item text-center py-2" style="border-top:1px solid #e9ecef;">
            <a href="{{ $_msgIndex }}" class="small text-primary font-weight-bold">
                <i class="fas fa-envelope-open-text mr-1"></i> Ver todos los mensajes
            </a>
        </div>

    </div>
</li>
