<figure data-feedback="fb:likes,fb:comments">
	<img data-process="false" src="{{ $uri->uri }}" />
    {{ if isset($imageDetails['alt']) }}
    <figcaption>{{ $imageDetails['alt'] }}</figcaption>
    {{ /if }}
</figure>
