<!-- Table LOGS -->
<div class="bg-white p-4 m-4 rounded-lg shadow-md">
    <div class="flex justify-evenly">
        <p class="text-2xl font-semibold text-gray-700">TABLE LOGS</p>
        <a href="{{ route('monitorar') }}">
        <button type="button" class="bg-green-500 text-white px-4 py-2 rounded">Monitorar</button>
        </a>
    </div>
    <div>
        <hr class="my-4 border-gray-300">
    </div>
    <div class="flex justify-evenly">
        @if (count($logs) == 0)
        <p class="text-2xl font-semibold text-gray-700">Tabela vazia</p>
        @else
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                <tr>
                    <th class="py-3 px-4">Ações</th>
                    @foreach (Schema::getColumnListing($logs->first()->getTable()) as $coluna)
                    <th class="py-3 px-4">{{ $coluna }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $index => $item)
                <tr class="hover:bg-gray-100 cursor-pointer">
                    <td class="py-3 px-4 flex space-x-2">

                        <!-- Formulário para excluir um log -->
                        <form action="{{ route('log.destroy', $item->id) }}" method="POST">
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
