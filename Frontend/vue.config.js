const { defineConfig } = require('@vue/cli-service');

module.exports = defineConfig({
  transpileDependencies: true
})

module.exports = {
  transpileDependencies: true,
  lintOnSave: false, // Отключение линтера
  pages: {
    client: {
      entry: 'src/client/main.js',
      template: 'public/index.html',
      filename: 'index.html',
      title: 'Client App',
      chunks: ['chunk-vendors', 'chunk-common', 'client'],
    },
    admin: {
      entry: 'src/admin/main.js',
      template: 'public/index.html',
      filename: 'admin.html',
      title: 'Admin App',
      chunks: ['chunk-vendors', 'chunk-common', 'admin'],
    },
  },
  devServer: {
    historyApiFallback: {
      rewrites: [
        { from: /^\/client/, to: '/client.html' },
        { from: /^\/admin/, to: '/admin.html' }
      ]
    }
  }
};
