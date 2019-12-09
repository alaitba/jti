<tr class="folder-row-{{$folder->id}}">
    <td><a style="text-decoration: none; cursor: context-menu;" data-url="{{route('admin.wysiwyg.objects', ['parent_id' => $folder->id])}}" class="handle-click" data-type="ajax-get" data-block-element="#editorModal .modal-body"><i class="fa fa-folder-o"></i> {{$folder->name}}</a></td>
    <td class="text-center">--</td>
    <td class="text-center">{{$folder->created_at->format('d.m.y, H:i')}}</td>
    <td class="text-center">
        <a href="#" style="text-decoration: none" data-url="{{route('admin.wysiwyg.folder.delete', ['id' => $folder->id])}}" class="pull-left handle-click" data-type="confirm" data-title="Удаление папки" data-message="Все файлы из папки будут перемещены в корень"><i class="la la-trash"></i></a>
        <a href="#" style="text-decoration: none" data-url="{{route('admin.wysiwyg.folder.edit', ['id' => $folder->id])}}" class="pull-right handle-click" data-type="modal" data-modal="manageFolderModal"><i class="la la-edit"></i></a>
    </td>
</tr>