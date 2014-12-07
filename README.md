StickPHP
========

PHP Library on PSR-2, PSR-3

## PHP version

PHP 5.3

## About StickPHP

PSR-2,PSR-3コーディング規約に沿って作られたPHPライブラリです。

MVC（Model, View, Controller）デザインパターンで実装しています。

### 各種ディレクトリの説明

#### config

ライブラリ全体の設定を管理する

#### controller

各種モデル・ビューを管理するコントローラクラスがあるディレクトリ

#### dao

各種データにアクセスするDAO（Data Access Object）クラスがあるディレクトリ

#### lib

ライブラリ内で共通で呼ばれるような機能を集めたディレクトリ

#### log

ロガークラスを集めたディレクトリ

#### manager

各種マネージャクラスを置くディレクトリ

#### template

テンプレート用ディレクトリ

StickPHPライブラリとしては汎用・サンプルのものをいくつか用意してある。

#### view

テンプレートを読み込み、変数を埋め込むなどの処理を行うビュークラスがあるディレクトリ

## ライブラリ制作ルール

* ロガーはPSR-3の思想に則って、差し替え可能な設計にする
* エラー時に、falseを返すかExceptionを投げるかはConfigクラスが管理する
* BaseクラスはStickPHPライブラリの設定を使わず切り離して使えるようにする
* その上でStickPHPライブラリとして実装したクラスも用意する
