<table class="table table-bordered">
    <thead>
        <tr>
            <th width="50" class="text-center">#</th>
            <th width="250">Файл</th>
            <th>Ссылка</th>
            <th width="120" class="text-center">Дата</th>
        </tr>
    </thead>
    <tbody>
        @foreach($files as $file)
            <tr>
                <td class="text-center">{{$file->id}}</td>
                <td><a href="{{asset('storage/media/' . $file->getOriginal('original_file_name'))}}" data-file-name="{{$file->getOriginal('client_file_name')}}" class="insert-file">{{$file->getOriginal('client_file_name')}}</a></td>
                <td><input onclick="this.select()" class="form-control" value="{{asset('storage/media/' . $file->getOriginal('original_file_name'))}}"></td>
                <td class="text-center">{{date("d.m.Y H:i", strtotime($file->created_at))}}</td>
            </tr>
        @endforeach
    </tbody>
</table>