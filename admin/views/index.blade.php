<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Bot Stats</title>
</head>
<body class="bg-teal-950 font-sans">

<!-- Users Stats Section -->
<div class="grid grid-cols-2 gap-4 place-content-center p-4">
    <div class="text-center bg-amber-200 p-4 m-4 rounded-lg">
        <p class="font-bold select-none">Super users - {{$superusers_count}}</p>
    </div>
    <div class="text-center bg-emerald-200 p-4 m-4 rounded-lg">
        <p class="font-bold select-none">Users - {{$users_count}}</p>
    </div>
</div>

<!-- Context Section -->
<div class="bg-cyan-500 m-4 rounded-lg">
    <div class="grid grid-cols-2 text-center font-bold p-4">
        <h1 class="m-4 text-3xl select-none">Context:</h1>
        <button onclick="clearContext()" class="bg-cyan-700 text-white rounded-lg hover:bg-cyan-800">HIDE</button>
    </div>
    <div id="context" class="p-4"></div>
</div>

<!-- Chats Table Section -->
<div class="flex flex-col bg-emerald-200 m-4 rounded-lg overflow-x-auto sm:-mx-6 lg:-mx-8">
    <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
        <table class="min-w-full text-left text-sm font-light text-center border-b-2 border-emerald-500">
            <thead class="font-medium font-bold">
            <tr>
                <th>ID</th>
                <th>Chat ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Language</th>
                <th>Rights</th>
                <th>Attempts</th>
                <th>Type</th>
                <th>Updated</th>
                <th>Context</th>
            </tr>
            </thead>
            <tbody>
            @foreach($chats as $chat)
                <tr class="hover:bg-@if($chat->rights === '0')gray-500
                                   @elseif($chat->rights === '1')emerald-500
                                   @elseif($chat->rights === '2')amber-500 @endif transition duration-200">
                    <td class="border-r border-emerald-500"><code>{{$chat->id}}</code></td>
                    <td class="border-r border-emerald-500"><code>{{$chat->chat_id}}</code></td>
                    <td class="border-r border-emerald-500">{{$chat->first_name}}</td>
                    <td class="border-r border-emerald-500">{{$chat->username}}</td>
                    <td class="border-r border-emerald-500">{{$chat->language}}</td>

                    <td class="border-r border-emerald-500 bg-@if($chat->rights === '0')gray-500
                                @elseif($chat->rights === '1')green-500
                                @elseif($chat->rights === '2')amber-500 @endif
                                @if($chat->rights !== '0') font-bold @endif">
                        @if($chat->rights === '0') No privileges
                        @elseif($chat->rights === '1') Pro
                        @elseif($chat->rights === '2') Creator @endif
                    </td>

                    <td class="border-r border-emerald-500">{{$chat->attempts}}</td>
                    <td class="border-r border-emerald-500">{{$chat->type}}</td>
                    <td class="border-r border-emerald-500">{{$chat->updated_at}}</td>

                    <td onclick="context({{$chat->context}})" class="border-r border-emerald-500 font-bold
                                text-emerald-200 hover:text-emerald-500 bg-emerald-500 text-center
                                select-none pointer-events-auto hover:bg-emerald-300">show ({{count(json_decode($chat->context, 1))}})
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function context(context) {
        const contextDiv = document.getElementById('context');
        contextDiv.innerHTML = '';

        Object.values(context).forEach((message) => {
            const messageDiv = document.createElement('div');
            messageDiv.innerHTML = `<p class="font-bold ml-6"> - ${message.role}</p>`;
            messageDiv.innerHTML += `<p class="italic ml-12">${message.content}</p>`;
            contextDiv.appendChild(messageDiv);
        });
    }

    function clearContext() {
        document.getElementById('context').innerHTML = '';
    }
</script>
</body>
</html>
