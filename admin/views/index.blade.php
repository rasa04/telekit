<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Document</title>
</head>
<body class="bg-teal-950">
    <div class="grid grid-cols-2 gap-4 place-content-center">
        <div class="text-center bg-amber-200 p-4 m-4 rounded-lg">
            <p class="font-bold select-none">Super users - {{$superusers_count}}</p>
        </div>
        <div class="text-center bg-emerald-200 p-4 m-4 rounded-lg">
            <p class="font-bold select-none">Users - {{$users_count}}</p>
        </div>
    </div>
    <div class="bg-cyan-500 m-4 rounded-lg">
        <div class="grid grid-cols-2 text-center font-bold">
            <h1 class="m-4 text-3xl select-none">Context:</h1>
            <button onclick="clearContext()" class="bg-cyan-700 text-white rounded-lg hover:bg-cyan-800">HIDE</button>
        </div>
        <div id="context"></div>
    </div>
    <div class="flex flex-col bg-emerald-200 m-4 rounded-lg">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                <table class="min-w-full text-left text-sm font-light text-center">
                    <thead class="border-b-2 border-emerald-500 font-medium font-bold">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Language</th>
                        <th>Rights</th>
                        <th>Attempts</th>
                        <th>Type</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Context</th>
                    </tr>
                    </thead>
                    <tbody>
                        @use(Illuminate\Support\Carbon)
                        @foreach($chats as $chat)
                            <tr
                                    @if($chat->rights === '0') class="hover:bg-gray-500 transition duration-200"
                                    @elseif($chat->rights === '1') class="hover:bg-emerald-500 transition duration-200"
                                    @elseif($chat->rights === '2') class="hover:bg-amber-500 transition duration-200"
                                    @endif
                            >
                                <td class="border-r border-emerald-500"><code>{{$chat->id}}</code></td>
                                <td class="border-r border-emerald-500">{{$chat->first_name}}</td>
                                <td class="border-r border-emerald-500">{{$chat->username}}</td>
                                <td class="border-r border-emerald-500">{{$chat->language}}</td>

                                @if($chat->rights === '0') <td class="border-r border-emerald-500 bg-gray-500">No privileges</td>
                                @elseif($chat->rights === '1') <td class="border-r border-emerald-500 bg-green-500 font-bold">Pro</td>
                                @elseif($chat->rights === '2') <td class="border-r border-emerald-500 bg-amber-500 font-bold">Creator</td>
                                @endif

                                <td class="border-r border-emerald-500">{{$chat->attempts}}</td>
                                <td class="border-r border-emerald-500">{{$chat->type}}</td>
                                <td class="border-r border-emerald-500">{{Carbon::parse($chat->created_at)->format('M d Y - h:i:s')}}</td>
                                <td class="border-r border-emerald-500">{{Carbon::parse($chat->updated_at)->format('M d Y - h:i:s')}}</td>

                                <td onclick="context({{$chat->context}})" class="border-r border-emerald-500 font-bold text-emerald-200 hover:text-emerald-500
                                    bg-emerald-500 text-center select-none pointer-events-auto hover:bg-emerald-300">show</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<script>
    function context(context)
    {
        document.getElementById('context').innerHTML = ''
        Object.values(context).forEach((message) => {
            document.getElementById('context').innerHTML += "<div>"
            document.getElementById('context').innerHTML += `<p class="font-bold ml-6"> - ${message.role}</>`
            document.getElementById('context').innerHTML += `<p class="italic ml-12">${message.content}</p>`
            document.getElementById('context').innerHTML += "</div>"
        })
        console.log(context)
    }
    function clearContext()
    {
        document.getElementById('context').innerHTML = ''
        console.log('cleared')
    }
</script>
</body>
</html>
