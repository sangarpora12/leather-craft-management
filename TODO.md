# TODO - Fix Operator Edit Bahan & Gmail Admin

## [ ] 1. Fix Form Duplikat (CRITICAL)
- File: `resources/views/operator/bahan-baku/edit.blade.php`
- Problem: 2 nested form → invalid HTML
- Action: Keep outer form `route('operator.bahan.update')`, remove inner

## [ ] 2. Enforce Notif Toggle
- File: `app/Http/Controllers/BahanBakuController.php`
- Add check `Pengaturan::where('nama_pengaturan', 'notif_bahan_masuk')->value('nilai') == '1'`

## [ ] 3. Gmail Config
- Check `.env` MAIL_MAILER=smtp, MAIL_USERNAME Gmail App Password
- Run `php artisan queue:work` if queueable

## [ ] 4. Test
- Operator edit bahan → save OK
- Bahan masuk → admin Gmail masuk

**Next: Fix form first**
