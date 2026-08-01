with open('routes/web.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Change route from /pekerja-master/{pekerja}/kinerja to /pekerja-master/kinerja/{pekerja}
# to avoid conflict with pekerja-master/{pekerja} (PUT/DELETE)
content = content.replace(
    "Route::get('/pekerja-master/{pekerja}/kinerja'",
    "Route::get('/pekerja-master/kinerja/{pekerja}'"
)

with open('routes/web.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Route fixed successfully')