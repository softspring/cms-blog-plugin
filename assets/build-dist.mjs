import {access, cp, mkdir, readFile, readdir, rm, writeFile} from 'node:fs/promises';
import {constants} from 'node:fs';
import {resolve} from 'node:path';
import {fileURLToPath} from 'node:url';

const root = new URL('.', import.meta.url);
const dist = new URL('./dist/', root);
const entries = [
  'scripts',
];

await rm(dist, {recursive: true, force: true});
await mkdir(dist, {recursive: true});

for (const entry of entries) {
  const source = new URL(`./${entry}/`, root);
  try {
    await access(source, constants.F_OK);
    await cp(source, new URL(`./${entry}/`, dist), {recursive: true});
  } catch {
    // Optional asset folders are skipped.
  }
}

const distRoot = fileURLToPath(dist);

async function rewriteScriptImports(directory) {
  let items = [];
  try {
    items = await readdir(directory, {withFileTypes: true});
  } catch {
    return;
  }

  for (const item of items) {
    const itemPath = resolve(directory, item.name);

    if (item.isDirectory()) {
      await rewriteScriptImports(itemPath);
      continue;
    }

    if (!item.isFile() || !item.name.endsWith('.js')) {
      continue;
    }

    let content = await readFile(itemPath, 'utf8');
    content = content.replaceAll("import './admin/content-edit/preview-article-list';", "import './admin/content-edit/preview-article-list.js';");
    await writeFile(itemPath, content);
  }
}

await rewriteScriptImports(resolve(distRoot, 'scripts'));
