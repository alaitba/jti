<tr class="row-{{ $item->id }}">
    <td>{{ $item->account_code }}</td>
    <td>{{ $item->tradepoint->account_name ?? '' }}</td>
    <td>{{ $item->tradepoint->city ?? ''}}</td>
    <td>-</td>
    <td>-</td>
    <td>{{ $item->bonus_portfolio }}</td>
    <td>{{ $item->plan_portfolio }}</td>
    <td>{{ $item->fact_portfolio }}</td>
    <td>{{ $item->salesplan2->dsd_till_date }}</td>
    <td>{{ $item->salesplan2->brand }}</td>
    <td>{{ $item->bonus_brand }}</td>
    <td>{{ $item->plan_brand }}</td>
    <td>{{ $item->fact_brand }}</td>
</tr>
