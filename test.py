import requests
r = requests.post(
    "https://api.deepai.org/api/text2img",
    data={
        'text': 'hey',
    },
    headers={'api-key': '9d8b806f-21d1-4e85-8c64-cdcafb2fafed'}
)
print(r.json())