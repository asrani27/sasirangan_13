#!/usr/bin/env python3
import sys, os

def write_view(rel_path, content):
    full_path = os.path.join('/Users/user/var/sasirangan_13/resources/views', rel_path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w', encoding='utf-8') as f:
        f.write(content.strip() + '\n')
    print(f"Wrote: {rel_path} ({len(content)} chars)")

if __name__ == '__main__':
    print("Writer helper ready")
