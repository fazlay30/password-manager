<h4>Hello, {{ $senderName }} has send you a group invitation.</h4>
{{--<button class="btn btn-success"><a href="{{ route('group-projects.invite.accept', $token) }}">Join @ {{ $groupProject->name }}</a></button>--}}
<button class="btn btn-success"><a href="{{ config('app.frontend_url') . '/accept-invitation/' . $token }}">Join @ {{ $groupProject->name }}</a></button>
