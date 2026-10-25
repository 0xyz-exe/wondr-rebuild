import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Check, ShieldCheck } from 'lucide-react';

type Product = { id: number; name: string; price_idr: number };
type Game = { id: number; name: string; category: string; mark: string; color: string; tone: string; products: Product[] };

export default function GameShow({ game }: { game: Game }) {
    const form = useForm({ game_id: String(game.id), product_id: '', player_id: '', server_id: '', customer_email: '', payment_method: 'sandbox', terms: false as boolean });
    const selected = game.products.find(product => String(product.id) === form.data.product_id);
    const formatIdr = (amount: number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);

    return <>
        <Head title={`Top up ${game.name} — WONDR`} />
        <main className="min-h-screen bg-[#f6f6f3] px-5 py-8 text-[#19221f] md:px-8">
            <div className="mx-auto max-w-5xl"><Link href="/" className="inline-flex items-center gap-2 text-sm font-bold text-[#68736a]"><ArrowLeft size={16}/> Kembali ke katalog</Link>
                <div className="mt-7 grid gap-8 md:grid-cols-[.8fr_1.2fr]">
                    <aside className="h-fit rounded-3xl bg-[#202b25] p-7 text-white"><span className="grid h-16 w-16 place-items-center rounded-2xl text-2xl font-black" style={{background:game.tone,color:game.color}}>{game.mark}</span><p className="mt-7 text-xs font-bold uppercase tracking-[.15em] text-[#c2f078]">{game.category}</p><h1 className="mt-2 text-3xl font-bold tracking-tight">Top up {game.name}</h1><p className="mt-4 text-sm leading-6 text-white/60">Masukkan ID pemain dengan teliti. WONDR tidak meminta kata sandi akun game.</p><div className="mt-8 flex items-start gap-3 border-t border-white/10 pt-5 text-xs leading-5 text-white/60"><ShieldCheck size={18} className="mt-0.5 shrink-0 text-[#c2f078]"/> Transaksi simulasi lokal saja. Katalog dan pembayaran nyata belum tersedia.</div></aside>
                    <form onSubmit={event => { event.preventDefault(); form.post('/orders'); }} className="rounded-3xl border border-[#e5e7df] bg-white p-6 md:p-8">
                        <h2 className="text-xl font-bold">Detail pesanan</h2><p className="mt-1 text-xs text-[#828b82]">Semua kolom bertanda * wajib diisi.</p>
                        <label className="mt-6 block text-xs font-bold">ID pemain *<input value={form.data.player_id} onChange={e=>form.setData('player_id',e.target.value)} className="mt-2 w-full rounded-xl border-[#e1e4dd] text-sm focus:border-[#8da76c] focus:ring-[#8da76c]" placeholder="Contoh: 123456789" required/></label>{form.errors.player_id&&<p className="mt-1 text-xs text-red-600">{form.errors.player_id}</p>}
                        <label className="mt-4 block text-xs font-bold">ID server <span className="font-normal text-[#929991]">(jika diperlukan)</span><input value={form.data.server_id} onChange={e=>form.setData('server_id',e.target.value)} className="mt-2 w-full rounded-xl border-[#e1e4dd] text-sm focus:border-[#8da76c] focus:ring-[#8da76c]" placeholder="Opsional"/></label>
                        <fieldset className="mt-6"><legend className="text-xs font-bold">Pilih nominal *</legend>{game.products.length ? <div className="mt-3 grid grid-cols-2 gap-2">{game.products.map(product=><button type="button" key={product.id} onClick={()=>form.setData('product_id',String(product.id))} className={`rounded-xl border p-3 text-left text-xs font-bold ${form.data.product_id===String(product.id)?'border-[#829c62] bg-[#f0f5e9]':'border-[#e5e7df] hover:bg-[#fafbf8]'}`}>{product.name}<span className="mt-1 block font-medium text-[#788277]">{formatIdr(product.price_idr)}</span></button>)}</div>:<p className="mt-3 rounded-xl bg-[#f7f8f5] p-4 text-xs text-[#778176]">Paket belum tersedia. Produk diaktifkan setelah supplier resmi terverifikasi.</p>}</fieldset>
                        <label className="mt-6 block text-xs font-bold">Email untuk status pesanan *<input type="email" value={form.data.customer_email} onChange={e=>form.setData('customer_email',e.target.value)} className="mt-2 w-full rounded-xl border-[#e1e4dd] text-sm focus:border-[#8da76c] focus:ring-[#8da76c]" placeholder="nama@email.com" required/></label>{form.errors.customer_email&&<p className="mt-1 text-xs text-red-600">{form.errors.customer_email}</p>}
                        <div className="mt-6 rounded-2xl bg-[#f6f7f3] p-4"><div className="flex justify-between text-xs text-[#7b847b]"><span>Metode pembayaran</span><span>Simulasi lokal</span></div><div className="mt-3 flex justify-between border-t border-[#e3e6dd] pt-3 text-sm font-extrabold"><span>Total</span><span>{selected?formatIdr(selected.price_idr):'Pilih nominal'}</span></div></div>
                        <label className="mt-5 flex items-start gap-2 text-xs leading-5 text-[#727b72]"><input type="checkbox" checked={form.data.terms} onChange={e=>form.setData('terms',e.target.checked)} className="mt-0.5 rounded border-[#cfd5ca] text-[#546d3e] focus:ring-[#829c62]" required/>Saya telah memeriksa ID pemain dan memahami bahwa fitur ini hanya simulasi lokal.</label>
                        {form.errors.product_id&&<p className="mt-2 text-xs text-red-600">{form.errors.product_id}</p>}{form.errors.terms&&<p className="mt-2 text-xs text-red-600">{form.errors.terms}</p>}
                        <button disabled={form.processing||!selected||game.products.length===0} className="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#19221f] px-4 py-3.5 text-sm font-bold text-white transition hover:bg-[#36453c] disabled:cursor-not-allowed disabled:opacity-40"><Check size={16}/>Buat pesanan simulasi</button><p className="mt-3 text-center text-[10px] text-[#929991]">Tidak ada pembayaran atau top-up nyata diproses.</p>
                    </form>
                </div>
            </div>
        </main>
    </>;
}
