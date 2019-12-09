<div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark " m-menu-vertical="1" m-menu-scrollable="1" m-menu-dropdown-timeout="500">
    <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
        {{--<li class="m-menu__item  m-menu__item--active" aria-haspopup="true">
            <a href="{{route('admin')}}" class="m-menu__link ">
                <i class="m-menu__link-icon flaticon-line-graph"></i>
                <span class="m-menu__link-title">
				    <span class="m-menu__link-wrap">
					    <span class="m-menu__link-text">Дашбоард</span>

					</span>
				</span>
            </a>
        </li>--}}
        @foreach(config('navigation.sections') as $section => $sectionData)
            @if(isset($sectionData))
                @if(Auth::guard('admins')->user()->hasAnyRole($sectionData['roles']))
                    <li class="m-menu__section ">
                        <h4 class="m-menu__section-text">{{$sectionData['title']}}</h4>
                        <i class="m-menu__section-icon flaticon-more-v2"></i>
                    </li>
                    @if(count($sectionData['items']))
                        @foreach($sectionData['items'] as $item)
                            @if($item['is_tree'] && Auth::guard('admins')->user()->hasAnyRole($item['roles']))
                                    <li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" m-menu-submenu-toggle="hover">
                                        <a href="javascript:;" class="m-menu__link m-menu__toggle">
                                            <i class="m-menu__link-icon {{ $item['icon'] }}"></i>
                                            <span class="m-menu__link-text">{{ $item['title'] }}</span>
                                            <i class="m-menu__ver-arrow la la-angle-right"></i>
                                        </a>
                                        <div class="m-menu__submenu ">
                                            <span class="m-menu__arrow"></span>
                                            <ul class="m-menu__subnav">
                                                <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true">
                                                    <span class="m-menu__link">
                                                        <span class="m-menu__link-text">{{ $item['title'] }}</span>
                                                    </span>
                                                </li>
                                                @foreach($item['children'] as $child)
                                                    @if (Auth::guard('admins')->user()->hasAnyRole($child['roles']))
                                                        <li class="m-menu__item  @if(Request::is($child['item_active_on'])) m-menu__item--active @endif" aria-haspopup="true">
                                                            <a href="{{route($child['route_name'], $child['route_params'])}}" class="m-menu__link ">
                                                                <i class="m-menu__link-bullet {{ $child['icon'] }}">
                                                                    <span></span>
                                                                </i>
                                                                <span class="m-menu__link-text">{{ $child['title'] }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                            @endif

                            @if(!$item['is_tree'] && Auth::guard('admins')->user()->hasAnyRole($item['roles']))
                                <li class="m-menu__item  @if(Request::is($item['item_active_on'])) m-menu__item--active @endif"
                                    aria-haspopup="true">
                                    <a href="{{ route($item['route_name']) }}" class="m-menu__link ">
                                        <i class="m-menu__link-icon {{$item['icon']}}"></i>
                                        <span class="m-menu__link-title">
                                <span class="m-menu__link-wrap">
                                    <span class="m-menu__link-text">{{$item['title']}}</span>
                                </span>
                            </span>
                                    </a>
                                </li>
                            @endif

                        @endforeach
                    @endif
                @endif
            @endif
        @endforeach
    </ul>
</div>
