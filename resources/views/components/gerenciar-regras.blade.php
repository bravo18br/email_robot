<!-- Table REGRAS -->
<div class="bg-white p-4 m-4 rounded-lg shadow-md">
    <div class="flex justify-evenly">
        <p class="text-2xl font-semibold text-gray-700">TABLE REGRAS</p>
        <button type="button" class="bg-green-500 text-white px-4 py-2 rounded" data-modal-target="#createRegraModal">Novo</button>
    </div>
    <div>
        <hr class="my-4 border-gray-300">
    </div>
    <div class="flex justify-evenly">
        @if (count($regras) == 0)
        <p class="text-2xl font-semibold text-gray-700">Tabela vazia</p>
        @else
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                <tr>
                    <th class="py-3 px-4">Ações</th>
                    @foreach (Schema::getColumnListing($regras->first()->getTable()) as $coluna)
                    <th class="py-3 px-4">{{ $coluna }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($regras as $index => $item)
                <tr class="hover:bg-gray-100 cursor-pointer">
                    <td class="py-3 px-4 flex space-x-2">

                        <!-- Botão para abrir o modal de edição de regra -->
                        <button type="button" class="bg-blue-500 text-white px-2 py-1 rounded" data-modal-target="#editRegraModal{{ $item->id }}" title="Editar registro">E</button>

                        <!-- Formulário para excluir uma regra -->
                        <form action="{{ route('regra.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-500 text-white px-2 py-1 rounded" type="submit" title="Excluir registro">X</button>
                        </form>
                    </td>
                    @foreach (Schema::getColumnListing($item->getTable()) as $coluna)
                    <td class="py-3 px-4">{{ $item->$coluna }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

<!-- Modal de Criação de Regra -->
<div id="createRegraModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
        <h2 class="text-xl font-semibold mb-4">Nova Regra</h2>
        <form action="{{ route('regra.store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="mb-4">
                <label for="descricao" class="block text-gray-700">Descrição</label>
                <input type="text" id="descricao" name="descricao" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('descricao') }}">
            </div>
            <div class="mb-4">
                <label for="subject" class="block text-gray-700">Assunto</label>
                <input type="text" id="subject" name="subject" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('subject') }}" required>
            </div>
            <div class="mb-4">
                <label for="webhook" class="block text-gray-700">Webhook</label>
                <input type="text" id="webhook" name="webhook" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('webhook') }}" required>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-gray-700">Status</label>
                <select id="status" name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="ativado" selected>Ativado</option>
                    <option value="desativado">Desativado</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="button" class="mr-2 bg-gray-500 text-white px-4 py-2 rounded" data-modal-close="#createRegraModal">Cancelar</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Criar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modais de Edição de Regras -->
@foreach ($regras as $item)
<div id="editRegraModal{{ $item->id }}" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
        <h2 class="text-xl font-semibold mb-4">Editar Regra</h2>
        <form action="{{ route('regra.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="descricao" class="block text-gray-700">Descrição</label>
                <input type="text" id="descricao" name="descricao" value="{{ $item->descricao }}" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label for="subject" class="block text-gray-700">Assunto</label>
                <input type="text" id="subject" name="subject" value="{{ $item->subject }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label for="webhook" class="block text-gray-700">Webhook</label>
                <input type="text" id="webhook" name="webhook" value="{{ $item->webhook }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-gray-700">Status</label>
                <select id="status" name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="ativado" {{ $item->status == 'ativado' ? 'selected' : '' }}>Ativado</option>
                    <option value="desativado" {{ $item->status == 'desativado' ? 'selected' : '' }}>Desativado</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="button" class="mr-2 bg-gray-500 text-white px-4 py-2 rounded" data-modal-close="#editRegraModal{{ $item->id }}">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
    // Script para abrir e fechar modais
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', () => {
            const modal = document.querySelector(button.dataset.modalTarget);
            modal.classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', () => {
            const modal = document.querySelector(button.dataset.modalClose);
            modal.classList.add('hidden');
        });
    });
</script>
