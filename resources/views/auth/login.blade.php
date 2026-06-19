<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Вход</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .puzzle-piece {
            width: 80px;
            height: 80px;
            border: 2px solid #ccc;
            cursor: grab;
        }
        .puzzle-piece:active {
            cursor: grabbing;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Авторизация</h1>
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Логин</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Пароль</label>
                    <input type="password" name="password" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Соберите пазл: перетащите картинки в правильном порядке 1-2-3-4</label>
                    <div id="puzzle" class="grid grid-cols-2 gap-0 w-[160px]"></div>
                    <input type="hidden" name="puzzle_order" id="puzzle_order">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Войти
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let piecesOrder = {!! json_encode($puzzleOrder) !!};
        const correctOrder = [1,2,3,4];
        let draggedItem = null;

        function renderPuzzle() {
            const container = document.getElementById('puzzle');
            container.innerHTML = '';
            piecesOrder.forEach((piece, idx) => {
                const img = document.createElement('img');
                img.src = `/images/puzzle/${piece}.png`;
                img.classList.add('puzzle-piece');
                img.setAttribute('data-index', idx);
                img.setAttribute('draggable', 'true');
                img.ondragstart = dragStart;
                img.ondragover = dragOver;
                img.ondrop = drop;
                container.appendChild(img);
            });
        }

        function dragStart(event) {
            draggedItem = this;
            event.dataTransfer.setData('text/plain', this.getAttribute('data-index'));
            event.dataTransfer.effectAllowed = 'move';
        }

        function dragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
        }

        function drop(event) {
            event.preventDefault();
            const target = event.target.closest('img');
            if (!target || target === draggedItem) return;
            const fromIndex = parseInt(draggedItem.getAttribute('data-index'));
            const toIndex = parseInt(target.getAttribute('data-index'));
            [piecesOrder[fromIndex], piecesOrder[toIndex]] = [piecesOrder[toIndex], piecesOrder[fromIndex]];
            renderPuzzle();
        }
        document.getElementById('loginForm').onsubmit = function() {
            document.getElementById('puzzle_order').value = piecesOrder.join(',');
        };

        renderPuzzle();
    </script>
</body>
</html>