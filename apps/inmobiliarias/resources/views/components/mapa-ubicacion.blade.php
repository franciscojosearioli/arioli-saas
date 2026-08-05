@props(['wireModel', 'modal', 'soloPoligono' => false])

{{--
    §04: ubicación como GEOMETRY (point o polygon) — mismo patrón que
    loteos (Leaflet + Leaflet.draw, WKT de ida y vuelta con el backend).
    wire:ignore es obligatorio: Livewire no debe tocar este div en ningún
    re-render, o destruye la instancia de Leaflet. Todo lo que entra/sale
    de Livewire pasa por $wire.set()/$wire.get() del campo wireModel,
    nunca por wire:model directo sobre el mapa.
--}}
@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script>
        function mapaUbicacion(config) {
            return {
                map: null,
                drawnItems: null,
                inicializado: false,
                init() {
                    window.addEventListener('open-modal', (evento) => {
                        if (evento.detail !== config.modal) return;
                        setTimeout(() => this.montarOInvalidar(), 150);
                    });
                },
                montarOInvalidar() {
                    if (this.inicializado) {
                        this.map.invalidateSize();
                        this.sincronizarDesdeWkt();
                        return;
                    }
                    this.montar();
                },
                montar() {
                    this.inicializado = true;
                    this.map = L.map(this.$refs.mapa).setView([-31.4201, -64.1888], 6);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap',
                    }).addTo(this.map);

                    this.drawnItems = new L.FeatureGroup();
                    this.map.addLayer(this.drawnItems);

                    const drawControl = new L.Control.Draw({
                        edit: { featureGroup: this.drawnItems },
                        draw: {
                            polygon: { allowIntersection: false, showArea: true },
                            marker: config.soloPoligono ? false : {},
                            polyline: false, rectangle: false, circle: false, circlemarker: false,
                        },
                    });
                    this.map.addControl(drawControl);

                    this.map.on(L.Draw.Event.CREATED, (e) => {
                        this.drawnItems.clearLayers();
                        this.drawnItems.addLayer(e.layer);
                        this.publicarWkt(e.layer);
                    });
                    this.map.on(L.Draw.Event.EDITED, (e) => {
                        e.layers.eachLayer((layer) => this.publicarWkt(layer));
                    });
                    this.map.on(L.Draw.Event.DELETED, () => {
                        this.$wire.set(config.wireModel, '');
                    });

                    this.sincronizarDesdeWkt();
                },
                sincronizarDesdeWkt() {
                    this.drawnItems.clearLayers();
                    const wkt = this.$wire.get(config.wireModel);
                    if (!wkt) return;

                    const capa = this.wktALayer(wkt);
                    if (!capa) return;

                    this.drawnItems.addLayer(capa);
                    const bounds = capa.getBounds ? capa.getBounds() : L.latLngBounds([capa.getLatLng()]);
                    this.map.fitBounds(bounds, { maxZoom: 16, padding: [16, 16] });
                },
                publicarWkt(layer) {
                    let wkt;
                    if (layer instanceof L.Marker) {
                        const ll = layer.getLatLng();
                        wkt = `POINT(${ll.lng} ${ll.lat})`;
                    } else {
                        const anillo = layer.getLatLngs()[0];
                        const puntos = anillo.map((ll) => `${ll.lng} ${ll.lat}`);
                        puntos.push(puntos[0]);
                        wkt = `POLYGON((${puntos.join(',')}))`;
                    }
                    this.$wire.set(config.wireModel, wkt);
                },
                wktALayer(wkt) {
                    const punto = wkt.match(/^POINT\(([-\d.]+)\s+([-\d.]+)\)$/i);
                    if (punto) {
                        return L.marker([parseFloat(punto[2]), parseFloat(punto[1])]);
                    }

                    const poligono = wkt.match(/^POLYGON\(\((.+)\)\)$/i);
                    if (poligono) {
                        const latlngs = poligono[1].split(',').map((par) => {
                            const [lng, lat] = par.trim().split(/\s+/).map(Number);
                            return [lat, lng];
                        });

                        return L.polygon(latlngs);
                    }

                    return null;
                },
            };
        }
    </script>
@endonce

<div wire:ignore
    x-data="mapaUbicacion({ wireModel: @js($wireModel), modal: @js($modal), soloPoligono: @js($soloPoligono) })"
    x-init="init()"
>
    <div x-ref="mapa" style="height: 280px;" class="rounded-md border border-gray-300"></div>
    <p class="text-xs text-gray-400 mt-1">
        @if ($soloPoligono)
            Dibujá el polígono general del desarrollo en el mapa (opcional).
        @else
            Marcá la ubicación en el mapa: un punto, o dibujá el polígono del lote (opcional).
        @endif
    </p>
</div>
