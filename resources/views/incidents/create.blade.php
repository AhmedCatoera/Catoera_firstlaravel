@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Create incident</h1>
    <p class="text-muted small mb-1">A unique incident ID is assigned automatically. Every incident is treated as high priority.</p>
    <p class="text-muted small mb-0">Pick the incident location on the map to capture precise coordinates.</p>
</div>

<div class="card card-ertms">
    <div class="card-body">
        <form method="post" action="{{ route('incidents.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="incident_type" class="form-label">Incident type</label>
                    <select name="incident_type" id="incident_type" class="form-select" required>
                        <option value="">Select category...</option>
                        @foreach($incidentTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('incident_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <label for="location" class="form-label">Location summary</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" required placeholder="Auto-filled from map pin">
                </div>
                <div class="col-md-6">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" name="latitude" id="latitude" class="form-control" value="{{ old('latitude') }}" readonly required>
                </div>
                <div class="col-md-6">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" name="longitude" id="longitude" class="form-control" value="{{ old('longitude') }}" readonly required>
                </div>
                <div class="col-12">
                    <label class="form-label">Pick incident point on map</label>
                    <div id="incidentMap" class="incident-map rounded border"></div>
                    <div class="form-text">Click anywhere on the map to place or move the marker.</div>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-danger">Save incident</button>
                <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (() => {
            const map = L.map('incidentMap').setView([14.5995, 120.9842], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const locationInput = document.getElementById('location');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            let marker = null;
            let geocodeController = null;

            async function reverseGeocode(lat, lng) {
                if (geocodeController) {
                    geocodeController.abort();
                }
                geocodeController = new AbortController();
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`;
                try {
                    const res = await fetch(url, {
                        signal: geocodeController.signal,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data && data.display_name) {
                        locationInput.value = data.display_name;
                    } else {
                        locationInput.value = `Lat ${Number(lat).toFixed(7)}, Lng ${Number(lng).toFixed(7)}`;
                    }
                } catch (e) {
                    locationInput.value = `Lat ${Number(lat).toFixed(7)}, Lng ${Number(lng).toFixed(7)}`;
                }
            }

            function setPoint(lat, lng) {
                if (!marker) {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    marker.on('dragend', (e) => {
                        const p = e.target.getLatLng();
                        setPoint(p.lat, p.lng);
                    });
                } else {
                    marker.setLatLng([lat, lng]);
                }

                latInput.value = Number(lat).toFixed(7);
                lngInput.value = Number(lng).toFixed(7);
                reverseGeocode(latInput.value, lngInput.value);
            }

            map.on('click', (e) => setPoint(e.latlng.lat, e.latlng.lng));

            if (latInput.value && lngInput.value) {
                setPoint(parseFloat(latInput.value), parseFloat(lngInput.value));
                map.setView([parseFloat(latInput.value), parseFloat(lngInput.value)], 15);
            }
        })();
    </script>
@endpush
