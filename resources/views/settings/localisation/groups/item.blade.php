<tr class="row-{{$item->id}}">
    <td class="text-center">{{$item->id}}</td>
    <td><a href="{{route('admin.settings.localisation.groups.items', ['groupId' => $item->id])}}" style="text-decoration: none">{{$item->name}}</a></td>
    <td class="text-center">{{$item->localisations_count}}</td>
    <td class="text-center">
        <a href="#" data-url="{{route('admin.settings.localisation.groups.edit', ['groupId' => $item->id])}}" class="handle-click" data-type="modal" data-modal="regularModal"><i class="la la-edit"></i></a>
    </td>
</tr>