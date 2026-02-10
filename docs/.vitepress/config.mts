import { defineConfig } from 'vitepress'
import { withMermaid } from 'vitepress-plugin-mermaid'

export default withMermaid(
  defineConfig({
    title: 'PHP Monad',
    description: '関数型プログラミングのモナド概念を PHP で実装したライブラリ',
    lang: 'ja',
    base: '/php-monad/',

    head: [
      ['meta', { name: 'theme-color', content: '#4F46E5' }],
      ['meta', { name: 'og:type', content: 'website' }],
      ['meta', { name: 'og:locale', content: 'ja_JP' }],
    ],

    themeConfig: {
      nav: [
        { text: 'ホーム', link: '/' },
        { text: 'ガイド', link: '/guide/getting-started' },
        { text: 'API', link: '/api/monad' },
        {
          text: 'API リファレンス (詳細)',
          link: '/api-reference/index.html',
          target: '_blank'
        }
      ],

      sidebar: {
        '/guide/': [
          {
            text: 'ガイド',
            items: [
              { text: 'はじめに', link: '/guide/getting-started' },
              { text: 'Option モナド', link: '/guide/option' },
              { text: 'Result モナド', link: '/guide/result' },
              { text: '実践例', link: '/guide/examples' }
            ]
          }
        ],
        '/api/': [
          {
            text: 'API リファレンス',
            items: [
              { text: 'Monad', link: '/api/monad' },
              { text: 'Option', link: '/api/option' },
              { text: 'Result', link: '/api/result' },
              { text: 'ヘルパー関数', link: '/api/functions' }
            ]
          }
        ]
      },

      socialLinks: [
        { icon: 'github', link: 'https://github.com/endou-mame/php-monad' }
      ],

      footer: {
        message: 'Released under the MIT License.',
        copyright: 'Copyright © 2024 EndouMame'
      },

      search: {
        provider: 'local',
        options: {
          translations: {
            button: {
              buttonText: '検索',
              buttonAriaLabel: '検索'
            },
            modal: {
              noResultsText: '結果が見つかりませんでした',
              resetButtonTitle: 'リセット',
              footer: {
                selectText: '選択',
                navigateText: '移動'
              }
            }
          }
        }
      },

      outline: {
        label: '目次',
        level: [2, 3]
      },

      docFooter: {
        prev: '前のページ',
        next: '次のページ'
      },

      lastUpdated: {
        text: '最終更新日'
      },

      returnToTopLabel: 'トップに戻る',
      sidebarMenuLabel: 'メニュー',
      darkModeSwitchLabel: 'テーマ切替'
    },

    mermaid: {},

    markdown: {
      theme: {
        light: 'github-light',
        dark: 'github-dark'
      }
    }
  })
)
