import re

with open('routes/web.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "Route::get('/pekerja-master/kinerja'",
    "Route::get('/pekerja-master/{pekerja}/kinerja'"
)

with open('routes/web.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Route fixed successfully')