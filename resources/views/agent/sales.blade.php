@extends('agent.layout')

@section('title', 'Ventes')
@section('page-title', 'Gestion des Ventes')

@section('content')
<div x-data="salesPage()" class="p-6 w-full">

    <!-- =======================
         FILTER / SEARCH CARD
    ======================= -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <!-- Card Title -->
        <h2 class="text-lg font-semibold mb-4">Recherche</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block mb-1 font-medium">Client</label>
                <input type="text" placeholder="Nom client" 
                       x-model="searchName" 
                       @input.debounce.300ms="fetchSales" 
                       class="border p-2 w-full rounded">
            </div>
            <div>
                <label class="block mb-1 font-medium">Date</label>
                <input type="date" x-model="searchDate" @change="fetchSales" class="border p-2 w-full rounded">
            </div>
            <div>
                <button type="button" @click="resetFilters" class="bg-gray-500 text-white px-4 py-2 rounded w-full hover:bg-gray-600 transition">
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>


    <!-- NEW SALE BUTTON -->
    <div class="mb-4 text-center">
        <button @click="openModal()" class="bg-blue-600 text-white px-6 py-2 rounded">
            + Nouvelle Vente
        </button>
    </div>

    <!-- SALE MODAL -->
    <div x-show="modalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-6xl p-6 rounded shadow-lg" @click.away="closeModal()">
            <h2 class="text-xl font-semibold mb-4 text-center" x-text="editMode ? 'Modifier Vente' : 'Nouvelle Vente'"></h2>

            <form :action="editMode ? `/agent/sales/${editSaleId}` : '{{ route('agent.sales.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- PRODUCTS -->
                <template x-for="(item,index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 mb-2 items-center">
                        <select class="col-span-5 border p-2"
                                :name="`products[${index}][product_id]`"
                                x-model="item.product_id"
                                @change="setPrice($event,index)" required>
                            <option value="">Produit</option>
                            @foreach($products as $stock)
                                <option value="{{ $stock->product_id }}"
                                        :selected="item.product_id == {{ $stock->product_id }}"
                                        data-price="{{ $stock->product->price }}">
                                    {{ $stock->product->name }}
                                </option>
                            @endforeach
                        </select>

                        <input type="number" class="col-span-2 border p-2"
                               :name="`products[${index}][quantity]`"
                               x-model.number="item.quantity"
                               min="1"
                               @input="calcTotal"
                               required>

                        <input type="text" class="col-span-2 border p-2"
                               :name="`products[${index}][unit_price]`"
                               x-model="item.price" readonly>

                        <input type="text" class="col-span-2 border p-2"
                               x-model="item.total" readonly>

                        <button type="button" class="col-span-1 text-red-600 font-bold" @click="removeItem(index)">✕</button>
                    </div>
                </template>

                <button type="button" class="text-blue-600 mb-4" @click="addItem()">+ Ajouter produit</button>

                <!-- CLIENT -->
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <input name="customer_name" placeholder="Nom client" class="border p-2" x-model="customer.name" required>
                    <input name="customer_email" placeholder="Email" class="border p-2" x-model="customer.email">
                    <input name="customer_phone" placeholder="Téléphone" class="border p-2" x-model="customer.phone">
                    <input name="customer_address" placeholder="Adresse" class="border p-2" x-model="customer.address">
                </div>

                <select name="invoice_type" class="border p-2 w-full mb-4" x-model="invoiceType" required>
                    <option value="proforma">Proforma</option>
                    <option value="facture">Facture</option>
                </select>

                <div class="text-right font-bold mb-4">
                    TOTAL: <span x-text="grandTotal.toFixed(2)"></span> KMF
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Annuler</button>
                    <button class="bg-green-600 text-white px-4 py-2 rounded">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SALES TABLE -->
    <div class="bg-white mt-6 p-4 w-full overflow-x-auto">
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Client</th>
                    <th class="border p-2">Invoice</th>
                    <th class="border p-2">Produits</th>
                    <th class="border p-2">Quantité</th>
                    <th class="border p-2">Région</th>
                    <th class="border p-2">Total (KMF)</th>
                    <th class="border p-2">Date</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="sale in sales" :key="sale.id">
                    <tr>
                        <td class="border p-2" x-text="sale.customer_name"></td>
                        <td class="border p-2" x-text="sale.invoice_type.charAt(0).toUpperCase() + sale.invoice_type.slice(1)"></td>
                        
                        <!-- NUMBERED PRODUCTS -->
                        <td class="border p-2 align-top">
                            <template x-for="(item, index) in sale.items" :key="item.id">
                                <div>
                                    <span x-text="`• ${item.product.name}`"></span>
                                </div>
                            </template>
                        </td>

                        <!-- NUMBERED QUANTITY -->
                        <td class="border p-2 align-top">
                            <template x-for="(item, index) in sale.items" :key="item.id">
                                <div>
                                    <span x-text="`• ${item.quantity}`"></span>
                                </div>
                            </template>
                        </td>

                        <td class="border p-2">
                            <span
                                x-text="[
                                    ...new Set(
                                        sale.items
                                            .map(i => i.region?.name)
                                            .filter(Boolean)
                                    )
                                ].join(', ') || 'N/A'"
                            ></span>
                        </td>
                        <td class="border p-2" x-text="parseFloat(sale.grand_total).toFixed(2)"></td>
                        <td class="border p-2" x-text="new Date(sale.created_at).toLocaleDateString('fr-FR')"></td>
                        <td class="border p-2 flex gap-2">
                            <a :href="`/agent/sales/${sale.id}/pdf`" target="_blank" class="bg-blue-600 text-white px-2 py-1 rounded text-sm">Facture</a>
                            <button @click="editSale(sale.id)" class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">Modifier</button>
                            <button @click="deleteSale(sale.id)" class="bg-red-600 text-white px-2 py-1 rounded text-sm">Supprimer</button>
                        </td>
                    </tr>
                </template>
                <tr x-show="sales.length === 0">
                    <td colspan="8" class="text-center p-4">Aucune vente trouvée</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function salesPage() {
    return {
        searchName: '{{ request("client_name") }}',
        searchDate: '{{ request("date") }}',
        sales: @json($sales),

        modalOpen: false,
        editMode: false,
        editSaleId: null,
        items: [{ product_id:'', quantity:1, price:0, total:0 }],
        grandTotal: 0,
        customer: { name:'', email:'', phone:'', address:'' },
        invoiceType: 'facture',

        fetchSales() {
            fetch(`/agent/sales/search?client_name=${this.searchName}&date=${this.searchDate}`)
                .then(res => res.json())
                .then(data => this.sales = data)
                .catch(err => console.error(err));
        },

        resetFilters() {
            this.searchName = '';
            this.searchDate = '';
            this.fetchSales();
        },

        openModal() { this.modalOpen = true },
        closeModal() { this.modalOpen = false; this.resetForm() },

        addItem() { this.items.push({ product_id:'', quantity:1, price:0, total:0 }) },
        removeItem(index) { this.items.splice(index,1); this.calcTotal() },

        setPrice(e,index) {
            const selected = e.target.selectedOptions[0];
            this.items[index].price = parseFloat(selected.dataset.price||0);
            this.calcTotal();
        },

        calcTotal() {
            this.grandTotal = 0;
            this.items.forEach(i => {
                i.total = (i.quantity * i.price).toFixed(2);
                this.grandTotal += parseFloat(i.total);
            });
        },

        resetForm() {
            this.editMode = false;
            this.editSaleId = null;
            this.items = [{ product_id:'', quantity:1, price:0, total:0 }];
            this.customer = { name:'', email:'', phone:'', address:'' };
            this.invoiceType = 'facture';
            this.calcTotal();
        },

        editSale(id) {
            fetch(`/agent/sales/${id}/edit`)
                .then(res => res.json())
                .then(data => {
                    this.editMode = true;
                    this.editSaleId = id;
                    this.items = data.items.map(i => ({
                        product_id: i.product_id,
                        quantity: i.quantity,
                        price: i.unit_price,
                        total: (i.quantity * i.unit_price).toFixed(2)
                    }));
                    this.customer.name = data.customer_name;
                    this.customer.email = data.customer_email;
                    this.customer.phone = data.customer_phone;
                    this.customer.address = data.customer_address;
                    this.invoiceType = data.invoice_type;
                    this.calcTotal();
                    this.modalOpen = true;
                })
                .catch(err => alert('Erreur lors du chargement de la vente.'));
        },

        deleteSale(id) {
            if(!confirm('Supprimer cette vente ?')) return;
            fetch(`/agent/sales/${id}`, {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            }).then(() => this.fetchSales());
        }
    }
}
</script>
@endsection
