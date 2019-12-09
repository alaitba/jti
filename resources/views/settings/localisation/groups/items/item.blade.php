<tr class="row-{{$item->id}}">
    <td class="text-center">{{$item->id}}</td>
    <td class="text-center">{{$item->name}}</td>
    <td class="text-center">{{$item->getInjectCode()}}</td>
    <td class="text-center">
        <a href="#" data-url="{{route('admin.settings.localisation.groups.items.edit', ['groupId' => $item->group_id, 'itemId' => $item->id])}}" class="handle-click" data-type="modal" data-modal="largeModal"><i class="la la-edit"></i></a>
    </td>
</tr>