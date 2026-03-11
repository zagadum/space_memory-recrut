import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'node:fs';
import path from 'node:path';

function syncDirectoryContents(sourceDir, destinationDir) {
    if (!fs.existsSync(sourceDir)) {
        return;
    }

    fs.mkdirSync(destinationDir, { recursive: true });

    for (const entry of fs.readdirSync(sourceDir, { withFileTypes: true })) {
        const sourcePath = path.join(sourceDir, entry.name);
        const destinationPath = path.join(destinationDir, entry.name);

        if (entry.isDirectory()) {
            syncDirectoryContents(sourcePath, destinationPath);
            continue;
        }

        fs.mkdirSync(path.dirname(destinationPath), { recursive: true });
        fs.copyFileSync(sourcePath, destinationPath);
    }
}

function removeSyncedFile(sourceRoot, destinationRoot, filePath) {
    const relativePath = path.relative(sourceRoot, filePath);

    if (relativePath.startsWith('..')) {
        return;
    }

    const destinationPath = path.join(destinationRoot, relativePath);

    if (fs.existsSync(destinationPath)) {
        fs.rmSync(destinationPath, { force: true });
    }
}

function syncStaticAssets() {
    let projectRoot = process.cwd();
    const mappings = [
        ['resources/images', 'public/images'],
        ['resources/fonts', 'public/fonts'],
    ];

    const syncAll = () => {
        for (const [source, destination] of mappings) {
            syncDirectoryContents(
                path.resolve(projectRoot, source),
                path.resolve(projectRoot, destination),
            );
        }
    };

    return {
        name: 'sync-static-assets-to-public',
        configResolved(config) {
            projectRoot = config.root;
            syncAll();
        },
        buildStart() {
            syncAll();
        },
        configureServer(server) {
            const sourceRoots = mappings.map(([source]) => path.resolve(projectRoot, source));

            server.watcher.add(sourceRoots);

            server.watcher.on('add', (filePath) => {
                for (let index = 0; index < mappings.length; index++) {
                    const [source, destination] = mappings[index];
                    const sourceRoot = path.resolve(projectRoot, source);

                    if (!filePath.startsWith(sourceRoot) || !fs.existsSync(filePath) || fs.statSync(filePath).isDirectory()) {
                        continue;
                    }

                    const destinationRoot = path.resolve(projectRoot, destination);
                    const relativePath = path.relative(sourceRoot, filePath);
                    const destinationPath = path.join(destinationRoot, relativePath);

                    fs.mkdirSync(path.dirname(destinationPath), { recursive: true });
                    fs.copyFileSync(filePath, destinationPath);
                }
            });

            server.watcher.on('change', (filePath) => {
                for (let index = 0; index < mappings.length; index++) {
                    const [source, destination] = mappings[index];
                    const sourceRoot = path.resolve(projectRoot, source);

                    if (!filePath.startsWith(sourceRoot) || !fs.existsSync(filePath) || fs.statSync(filePath).isDirectory()) {
                        continue;
                    }

                    const destinationRoot = path.resolve(projectRoot, destination);
                    const relativePath = path.relative(sourceRoot, filePath);
                    const destinationPath = path.join(destinationRoot, relativePath);

                    fs.mkdirSync(path.dirname(destinationPath), { recursive: true });
                    fs.copyFileSync(filePath, destinationPath);
                }
            });

            server.watcher.on('unlink', (filePath) => {
                for (const [source, destination] of mappings) {
                    removeSyncedFile(
                        path.resolve(projectRoot, source),
                        path.resolve(projectRoot, destination),
                        filePath,
                    );
                }
            });
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/saas/admin.scss', 'resources/saas/fonts.scss', 'resources/saas/fonts_pl.scss'],
            refresh: true,
        }),
        tailwindcss(),
        syncStaticAssets(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
