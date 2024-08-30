<!-- Notificação de erro -->
@if(session('erro'))
<div id="errorNotification" class="fixed top-0 left-1/2 transform -translate-x-1/2 bg-red-500 text-white p-4 rounded-b-lg shadow-md transition-transform duration-500 translate-y-[-100%] z-50">
    <p>{{ session('erro') }}</p>
    @if($errors->any())
    <ul class="mt-2">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const elementosNotificacao = ['errorNotification'];
        elementosNotificacao.forEach(function(id) {
            const notification = document.getElementById(id);
            if (notification) {
                setTimeout(() => {
                    notification.classList.remove('translate-y-[-100%]');
                    notification.classList.add('translate-y-0');
                }, 100);
                setTimeout(() => {
                    notification.classList.remove('translate-y-0');
                    notification.classList.add('translate-y-[-100%]');
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
            }
        });
    });
</script>
