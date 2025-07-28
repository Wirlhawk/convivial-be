<div style="display: flex; gap: 4px; flex-wrap: wrap;">
    @foreach(json_decode($getState(), true) ?? [] as $img)
        <img src="{{ asset('storage/' . $img) }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; background: #eee;" alt="Image" />
    @endforeach
</div> 