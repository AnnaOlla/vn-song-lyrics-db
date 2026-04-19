<?php

namespace Localization\Functions
{
	function localizeLanguageName(array $entity): string
	{
		return $entity['language_ja_name'];
	}

	function localizeLanguageKey(): string
	{
		return 'language_ja_name';
	}

	function localizeTranslationNumber(int $number): string
	{
		// Japanese only:
		$halfWidthDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
		$fullWidthDigits = ['０', '１', '２', '３', '４', '５', '６', '７', '８', '９'];
		
		return '＃'.str_replace($halfWidthDigits, $fullWidthDigits, (string)$number);
		
		// return '#'.$number;
	}
	
	use InputError;
	
	function localizeInputError(InputError $error): string|null
	{
		switch ($error)
		{
			case InputError::None:
				return null;
			
			case InputError::CaptchaInvalid:
				return 'コードは正しくなかったです。';
			
			case InputError::EmailNotFound:
				return 'メールは見つかっていません。';
			
			case InputError::EmailInvalid:
				return 'メールの書き方は正しくなかったです。';
			
			case InputError::EmailTaken:
				return 'メールは使ってはいけません。';
			
			case InputError::UsernameForbiddenSymbols:
				return 'ユーザーネームは許さない文字を持っています。';
			
			case InputError::UsernameLengthIncorrect:
				return 'ユーザーネームの長さは正しくなかったです。';
			
			case InputError::UsernameTaken:
				return 'ユーザーネームはもう使われています。';
			
			case InputError::IncorrectPassword:
				return 'パスワードは正しくなかったです。';
			
			case InputError::PasswordForbiddenSymbols:
				return 'パスワードは許さない文字を持っています。';
			
			case InputError::PasswordLengthIncorrect:
				return 'パスワードの長さは正しくなかったです。';
			
			default:
				throw new Exception(__FUNCTION__.': value '.$error->name.' was not found');
		}
	}
}

namespace Localization\InputError
{
	const CaptchaInvalid           = 'コードは正しくなかったです。';
	
	const EmptyEmail               = 'メールは入力しませんでした。';
	const EmailNotFound            = 'メールは見つかっていません。';
	const EmptyPassword            = 'パスワードは入力しませんでした。';
	const IncorrectPassword        = 'パスワードは正しくなかったです。';
	
	const UsernameTrimmable        = 'ユーザーネームは始めと終わりに見えない文字を持ってはいけません。';
	const UsernameForbiddenSymbols = 'ユーザーネームはローマ字と数字しか持ってはいけません。';
	const UsernameLengthIncorrect  = 'ユーザーネームの長さは４から３２までだけです。';
	const UsernameTaken            = 'ユーザーネームはもう使われています。';
	
	const EmailTaken               = 'メールは使ってはいけません。';
	const EmailInvalid             = 'メールは正しくない。';
	const EmailNotExists           = 'メールを確かめられませんでした。';
	
	const PasswordForbiddenSymbols = 'パスワードはローマ字と数字しか持ってはいけません。';
	const PasswordLengthIncorrect  = 'パスワードの長さは４から３２までだけです。';
	
	const AccountNotVerified       = 'アカウントはまだ確認していません。受信箱とスパムチェックしてください。';
	const MailSendFailed           = '確認のメールを送れませんでした。';
}

namespace Localization\HomePage
{
	const Heading          = 'Visual Novel Song Lyrics Database';
	const DescriptionOne   = '「vn-song-lyrics-db.ru」はビジュアルノベルにある歌のデータベースになりたいです。';
	const DescriptionTwo   = 'ビジュアルノベルの歌はメインストリームではなき、歌詞と翻訳を探すのは難しです。';
	const DescriptionThree = 'このウエッブサイトはウィキという百科事典ような所です。誰でも力になることができます。';
	const LastAlbums       = '最近加えたアルバム';
	const LastLyrics       = '最近加えた歌詞';
	const LastTranslations = '最近加えた翻訳';
}

namespace Localization\Header
{
	const HomePage        = 'vn-song-lyrics-db';
	
	const GameList        = 'ゲーム';
	const AlbumList       = 'アルバム';
	const ArtistList      = 'カシュ';
	const CharacterList   = 'キャラ';
	const SongList        = 'ウタ';
	const TranslationList = 'ホンヤク';
	const Feedback        = 'コメント';
	
	const LogIn           = 'ログイン';
	const SignUp          = 'サインアップ';
	const LogOut          = 'ログアウト';
}

namespace Localization\Footer
{
	const About         = 'サイトについて';
	const Policy        = 'サイトのポリシー';
	const BehaviorRules = 'サイトのルール';
	const WritingGuide  = '文章作法';
}

namespace Localization\LogInPage
{
	const Heading                = 'ログイン';
	
	const HintPassword           = 'なくしたなら、「support@vn-song-lyrics-db.ru」へメールを送ってください。';
	
	const Email                  = 'メール';
	const Password               = 'パスワード';
	const Submit                 = 'ログイン';
}

namespace Localization\SignUpPage
{
	const Heading  = 'サインアップ';
	
	const Username = 'ユーザーネーム';
	const Password = 'パスワード';
	const Email    = 'メール';
	const Submit   = 'サインアップ';
	
	const HintUsername = '使われる文字：A-Z、a-z、0-9。長さ：4-32。';
	const HintEmail    = '確認するためだけです。メールを送りません。';
	const HintPassword = '使われる文字：A-Z、a-z、0-9。長さ：4-32。';
	
	const Confirmation = '「サインアップ」を押して、あなたは以下のルールを受け入れます：';
	const Policy       = 'サイトのポリシー';
	const Rules        = 'サイトのルール';
	const WritingGuide = '文章作法';
	const Warning      = 'ルールを読むためにお時間をいただけてください。';
	
	const AwaitingVerification = 'アカウントは確認するために、メールを送りました。受信箱とスパムチェックしてください。';
}

namespace Localization\GameListPage
{
	const Heading = 'ゲームのリスト';
	const AddGame = 'ゲームを加える';
}

namespace Localization\AlbumListPage
{
	const Heading  = 'アルバムのリスト';
	const AddAlbum = 'アルバムを加える';
}

namespace Localization\ArtistListPage
{
	const Heading   = 'カシュのリスト';
	const AddArtist = 'カシュを加える';
}

namespace Localization\CharacterListPage
{
	const Heading      = 'キャラのリスト';
	const AddCharacter = 'キャラを加える';
}

namespace Localization\SongListPage
{
	const Heading     = 'ウタのリスト';
	const SongName    = 'ウタのタイトル';
}

namespace Localization\TranslationListPage
{
	const Heading = 'ホンヤクのリスト';
}

namespace Localization\FeedbackPage
{
	const Heading             = 'コメント';
	
	const Introduction        = 'このページでウエブサイトについてコメントを書くことができます。';
	const MessagePublic       = '全てのコメントは公開されています。';
	const AboutAnswer         = 'スタッフは全部のコメントを読んでいて、返事もします。';
	const SymbolLimit         = 'メッセージの長さは５００文字以下です。';
	
	const TextareaPlaceholder = 'あなたのメッセージ';
	const AnonymousAuthor     = 'アノニマス';
	const ReplyFromStaff      = 'スタッフ';
	const Submit              = '出す';
	
	const Delete              = '削除';
	const SendReply           = '出す／替える';
}

namespace Localization\GamePage
{
	const Details           = '詳細：';
	const RelatedCharacters = '関係があるキャラ';
	const RelatedAlbums     = '関係があるアルバム';
}

namespace Localization\AlbumPage
{
	const Details      = '詳細：';
	const SongCount    = 'トラックの数：';
	
	const SongList     = 'トラックリスト';
	const NoSongsAdded = 'トラックは一つもまだ加えられた。';
	const RelatedGames = '関係があるゲーム';
	
	const DiscNumber   = 'ディスク';
	const TrackNumber  = 'トラック';
	const SongName     = 'タイトル';
	
	const AddSong      = 'トラックを加える';
	const EditSong     = 'トラックを編集';
	const FillAlbum    = 'vgmdb.netを使ってトラックで満たす';
}

namespace Localization\ArtistPage
{
	const Details      = '詳細：';
	const AliasOf      = '通称名を持っているアーチスト：';
	const Aliases      = '通称名';
	const RelatedSongs = '関係があるウタ';
}

namespace Localization\CharacterPage
{
	const Details      = '詳細：';
	const RelatedGames = '関係があるゲーム';
	const RelatedSongs = '関係があるウタ';
}

namespace Localization\SongPage
{
	// nothing here yet
}

namespace Localization\LyricsPage
{
	const LyricsHeadingStart          = '歌詞：';
	const LyricsHeadingEnd            = '';
	
	const TranslationHeadingStart     = '翻訳：「';
	const TranslationHeadingMiddle    = '」（';
	const TranslationHeadingEnd       = '）';
	
	const NoLyricsAdded               = '歌詞はまだ加えていません。';
	const AddLyrics                   = '歌詞を加える';
	const EditLyrics                  = '歌詞を編集';
	const DeleteLyrics                = '歌詞を削除';
	const ReportLyrics                = '歌詞を訴え出る';
	
	const AddLyricsUnavailableNoVoice = 'このウタはインストです。';
	const AddLyricsUnavailableForCopy = 'でも、原作のウタに加えることができます。';
	
	const AddTranslation              = '翻訳を加える';
	const EditTranslation             = '翻訳を編集';
	const DeleteTranslation           = '翻訳を削除';
	const ReportTranslation           = '翻訳を訴え出る';
	
	const LyricsOf                    = '歌詞：';
	const LyricsNotes                 = 'コメント';
	const LyricsNoNotes               = 'コメントはありません。';
	
	const TranslationOf               = '翻訳：';
	const TranslationNotes            = 'コメント';
	const TranslationNoNotes          = 'コメントはありません。';
	
	const OriginalSong                = '原作の歌：';
	const Album                       = 'アルバム：';
	const ShowLyricsOnly              = '歌：';
	const TranslationList             = '翻訳：';
	const PerformerList               = '歌唱：';
	
	const ListElementSeparator        = '、';
	const CvOpeningBracket            = '（ＣＶ．';
	const CvClosingBracket            = '）';
}

namespace Localization\GameEditorPage
{
	const HeadingAdd         = 'ゲームを加える';
	const HeadingEdit        = 'ゲームを編集：';
	
	const OriginalName       = '原名';
	const TransliteratedName = 'ローマ字で書いた原名';
	const LocalizedName      = '翻訳した原名';
	const VndbLink           = 'vndb.orgにある詳細にリンク';
	const UploadNewLogo      = '新しいロゴをアップロード';
	const OldLogo            = '今のロゴ';
	const NewLogo            = '新しいロゴ';
	const Logo               = 'ロゴ';
	const RelatedAlbums      = '関係があるアルバム';
	const RelatedCharacters  = '関係があるキャラ';
}

namespace Localization\AlbumEditorPage
{
	const HeadingAdd         = 'アルバムを加える';
	const HeadingEdit        = 'アルバムを編集：';
	
	const OriginalName       = '原名';
	const TransliteratedName = 'ローマ字で書いた原名';
	const LocalizedName      = '翻訳した原名';
	const VgmdbLink          = 'vgmdb.netにある詳細にリンク';
	const UploadNewCover     = '新しいカバーをアップロード';
	const OldCover           = '今のカバー';
	const NewCover           = '新しいカバー';
	const Cover              = 'カバー';
	const RelatedGames       = '関係があるゲーム';
	const SongCount          = 'トラックの数';
}

namespace Localization\ArtistEditorPage
{
	const HeadingAdd         = 'カシュを加える';
	const HeadingEdit        = 'カシュを編集：';
	
	const OriginalName       = '自分の言語で書いた名前';
	const TransliteratedName = 'ローマ字で書いた名前';
	const LocalizedName      = '言語のルールに化した名前';
	const VgmdbLink          = 'vgmdb.netにある詳細にリンク';
	const UploadNewPhoto     = '新しい写真をアップロード';
	const OldPhoto           = '今の写真';
	const NewPhoto           = '新しい写真';
	const Photo              = '写真';
	const OriginalArtist     = 'この通称名を使うアーチスト';
}

namespace Localization\CharacterEditorPage
{
	const HeadingAdd         = 'キャラを加える';
	const HeadingEdit        = 'キャラを編集：';
	
	const OriginalName       = '自分の言語で書いた名前';
	const TransliteratedName = 'ローマ字で書いた名前';
	const LocalizedName      = '言語のルールに化した名前';
	const VndbLink           = 'vndb.orgにある詳細にリンク';
	const UploadNewImage     = '新しいイメージをアップロード';
	const OldImage           = '今のメージ';
	const NewImage           = '新しいイメージ';
	const Image              = 'イメージ';
	const RelatedGames       = '関係があるゲーム';
}

namespace Localization\SongEditorPage
{
	const HeadingAdd         = '歌を加える';
	const HeadingEdit        = '歌を編集：';
	
	const NextDisc           = 'ディスク: +1';
	const PreviousDisc       = 'ディスク: -1';
	const DiscAndTrack       = 'ディスクとトラック';
	const DiscNumber         = 'ディスク';
	const TrackNumber        = 'トラック';
	const OriginalName       = '原名';
	const TransliteratedName = 'ローマ字で書いた原名';
	const LocalizedName      = '翻訳した原名';
	const HasVocal           = '歌詞がある？';
	const HasVocalTrue       = 'ある';
	const HasVocalFalse      = 'ない';
	
	const SubmitLastSong     = '最後の歌を出す';
	const SubmitNonLastSong  = 'この歌を出し、次の歌を';
	const SubmitChanges      = '変化を出す';
}

namespace Localization\LyricsEditorPage
{
	const HeadingAdd         = '歌詞を加える：';
	const HeadingEdit        = '歌詞を編集：';
	const ArtistAndCharacter = 'カシュとキャラ';
	const PerformsAs         = 'にＣＶされる';
	const OriginalSong       = '原作の歌';
	const Language           = '言語';
	const Lyrics             = '歌詞';
	const Notes              = 'コメント';
}

namespace Localization\TranslationEditorPage
{
	const HeadingAdd        = '翻訳を加える';
	const HeadingEdit       = '翻訳を編集';
	
	const SourceLanguage    = '歌の言語';
	const TargetLanguage    = '翻訳の言語';
	const SongName          = '歌のタイトル';
	const TranslationName   = '翻訳のタイトル';
	const SongLyrics        = '歌の歌詞';
	const TranslationLyrics = '翻訳の歌詞';
	const SongNotes         = '歌のコメント';
	const TranslationNotes  = '翻訳のコメント';
}

namespace Localization\FillAlbumEditorPage
{
	const Heading            = 'アルバムを満たし：';
	
	const DiscNumber         = 'ディスク';
	const TrackNumber        = 'トラック';
	const OriginalName       = '原名';
	const TransliteratedName = 'ローマ字で書いた原名';
	const LocalizedName      = '翻訳した原名';
	
	const HasVocal           = '歌詞がある？';
	const HasVocalTrue       = 'ある';
	const HasVocalFalse      = 'ない';
}

namespace Localization\DeleteEntityPage
{
	const DeleteGame        = 'ゲームを削除：';
	const DeleteAlbum       = 'アルバムを削除：';
	const DeleteArtist      = 'カシュを削除：';
	const DeleteCharacter   = 'キャラを削除：';
	const DeleteSong        = 'ウタを削除：';
	const DeleteLyrics      = '歌詞を削除：';
	const DeleteTranslation = '翻訳を削除：';
	
	const Game              = 'ゲーム';
	const Album             = 'アルバム';
	const Artist            = 'カシュ';
	const Character         = 'キャラ';
	const Song              = 'ウタ';
	const Lyrics            = '歌詞';
	const Translation       = '翻訳';
	
	const Introduction      = '削除は確かですか？';
	const Warning           = '削除を取り消しはできません。';
}

namespace Localization\ReportPage
{
	const Heading            = '訴え出る：';
	
	const Introduction       = 'これは訴え出るページです。';
	const AboutReportContent = '未完成なとか、不正解なとか、不適切なとか、情報が見つかったそうです。詳しくください。';
	const NoActionWarning    = '訴え出を診ることに時間がかかる。行動が足りないことだと可能性があります。';
	const ReplyOpportunity   = '返事をもらいたいなら、support@vn-song-lyrics-db.ruに訴えのメールを送ってください。';
	const SymbolLimitWarning = 'ここでメッセージの長さは２５０文字以下です。';
	const Redirect           = '訴えを出すあと、以前のページに帰ります。';
	
	const Game               = '（ゲーム）';
	const Album              = '（アルバム）';
	const Artist             = '（カシュ）';
	const Character          = '（キャラ）';
	const Song               = '（ウタ）';
	const Lyrics             = '（カシ）';
	const Translation        = '（ホンヤク）';
}

namespace Localization\UserPage
{
	const User                 = 'ユーザー：';
	const Role                 = '役：';
	
	const AccountControl       = 'アカウントコントロール';
	const ChangeAccountData    = 'データを編集';
	const DeleteAccount        = 'アカウントを削除';
	
	const Contributions        = '貢献';
	const RelatedGames         = 'ゲーム：';
	const RelatedAlbums        = 'アルバム：';
	const RelatedArtists       = 'カシュ：';
	const RelatedCharacters    = 'キャラ：';
	const RelatedSongs         = 'ウタ：';
	const RelatedTranslations  = 'ホンヤク：';
}

namespace Localization\UserAccountDataPage
{
	const Edit            = '：編集';
	const AccountData     = 'アカウントデータ';
	
	const Username        = 'ユーザーネーム';
	const Email           = 'メール';
	const NewPassword     = 'パスワード';
	const NewPasswordNote = 'パスワードを編集の場合だけ満たしてください';
	const OldPassword     = '今のパスワード';
}

namespace Localization\UserAccountDeletePage
{
	const Delete       = '：削除';
	const AccountData  = '注意！';
	
	const Warning1     = 'これはアカウントを削除のぺーじです。';
	const Warning2     = 'ウェブサイトはあなたのデータを忘れる。';
	const Warning3     = 'あなたの貢献は消しません。「削除したユーザー」に変わります。';
	const Warning4     = '削除はすぐに発効します。取り消しはできません。';
	const Confirmation = 'ご確認のため、パスワードを入力してください。';
	
	const Password     = 'パスワード';
}

namespace Localization\ImageAltText
{
	const NoGameLogo       = 'ゲームのロゴはありません';
	const NoAlbumCover     = 'アルバムのカバーはありません';
	const NoArtistPhoto    = '歌手の写真はありません';
	const NoCharacterImage = 'キャラのイメージはありません';
	
	const GameLogoOf       = 'というゲームのロゴ';
	const AlbumCoverOf     = 'というアルバムのカバー';
	const ArtistPhotoOf    = '歌手の写真';
	const CharacterImageOf = 'キャラのイメージ';
}

namespace Localization\TimestampString
{
	const Added       = '加え：';
	const Updated     = '修正：';
	const Reviewed    = '確認：';
	const By          = '、';
	const DeletedUser = '削除したユーザー';
	const Delimeter   = '';
}

namespace Localization\ModerationStatus
{
	const RelationStatus = '関係のステータス：';
	const Status         = 'ステータス：';
	const Unchecked      = 'まだ確認されなかった';
	const Checked        = '確認された';
	const Hidden         = '隠された';
	const Unknown        = '不明';
}

namespace Localization\Tooltip
{
	const UserVisitor         = '貢献するのようにログインしなければなりません';
	const UserViolator        = 'あなたのアカウントは限られている';
	const UserNotAuthor       = '出した者はあなたではありません';
	const NotOriginalSong     = '原作の歌のページに訪れてください';
	const InfoHidden          = 'サイトは公式の手紙もらって、アクセスを限りました';
	const InfoChecked         = '情報はスタッフに確認されました';
	const SongHasTranslations = '歌は翻訳があります';
	const OriginalLanguage    = 'これは歌の言語です';
	const AlreadyTranslated   = 'あなたはこの言語に歌をもう翻訳しました';
}

namespace Localization\Controls
{
	const Report       = '情報を訴え出る';
	const Edit         = '情報を編集';
	const Delete       = '情報を削除';
	
	const SearchHeading     = 'データベースの中から探す…';
	const SearchPlaceholder = '探すように入力して…';
	const SearchButton      = '探す';
	const PageHeading       = 'ページ';
	const LimitHeading      = '１パージに記録の数';
	const NoLimit           = 'すべて';
	
	const FilterPage   = 'このページから探す…';
	const Textarea     = '入力して…';
	
	const ChooseFile   = 'ファイルを選ぶ（ｍａｘ：５１２ＫｉＢ）…';
	const FileTooBig   = 'ファイルは大きすぎています…';
	
	const Cancel       = '戻る';
	const Confirmation = '確認します';
	const Submit       = '出す';
}

namespace Localization\TooltipWindow
{
	const DefaultHeading = 'ヒント';
	const DefaultContent = 'ここに役に立つ情報があります。'.
							'</br></br>ページ要素をホバーしてください。';
}

namespace Localization\GameEditorPage\TooltipHeading
{
	const OriginalName       = '原名';
	const TransliteratedName = 'ローマ字で書いた原名';
	const LocalizedName      = '翻訳した原名';
	const OldLogo            = '今のロゴ';
	const NewLogo            = '新しいロゴ';
	const Logo               = 'ロゴ';
	const VndbLink           = 'vndb.orgにリンク';
	const RelatedAlbums      = '関係があるアルバム';
	const RelatedCharacters  = '関係があるキャラ';
	const Controls           = 'コントロール';
}

namespace Localization\GameEditorPage\TooltipContent
{
	const OriginalName       = 'ゲームの原名です。<br/><br/>'.
	                           'まずは、ゲームがあるかどうか、チェックしてください。<br/><br/>'.
							   '* = 必要';
	const TransliteratedName = 'ローマ字で書いた原名です。<br/><br/>'.
	                           'まずは、ローマ字に変換し方を文章作法に見てください。リンクはページの下であります。<br/><br/>'.
							   'ローマ字はＡＳＣＩＩだけを使っています。<br/><br/>'.
							   '* = 必要';
	const LocalizedName      = '外国で使った名前です。逆もまた然り：ゲームを作った国の言語に変換した名前もです。<br/><br/>'.
	                           'ゲームが外国へ出ませんでしたならば、何も書かないでください。';
	const OldLogo            = '今のアップロードしているイメージ。';
	const NewLogo            = 'ロゴを替えてほしいなら、別のイメージをアップロードしてもいいです。<br/><br/>'.
	                           '上限の大きさ:５１２キロバイト。<br/><br/>'.
							   'おすすめは、同じ幅と高さのイメージです。';
	const Logo               = 'ゲームのロゴです。<br/><br/>'.
	                           '上限の大きさ:５１２キロバイト。<br/><br/>'.
							   'おすすめは、同じ幅と高さのイメージです。';
	const VndbLink           = 'vndb.orgデータベースのゲームの情報にリンクです。<br/><br/>'.
	                           'vndb.orgはインターネットで一番大きいビジュアルノベルのデータベースだかもしれません。<br/><br/>'.
							   'このサイトは詳しい情報ではありません。<br/><br/>'.
							   'そして、vndb.orgにリンクを入力してください。';
	const RelatedAlbums      = 'ゲームに関係があるアルバムはデータベースにあるなら、ここに選んでください。<br/><br/>'.
	                           '「＋」のボタンを押して、もう一つの関係を加えられます。<br/><br/>'.
	                           '「－」のボタンを押して、関係を削除します。<br/><br/>'.
							   '関係があるアルバムはないなら、何も選らなくてもいいです。後も関係を加えてもいいです。<br/><br/>'.
	                           'フィールドは編集できないなら、スタッフは関係をもう確認しました。';
	const RelatedCharacters  = 'ゲームに関係があるキャラはデータベースにあるなら、ここに選んでください。<br/><br/>'.
	                           '「＋」のボタンを押して、もう一つの関係を加えられます。<br/><br/>'.
	                           '「－」のボタンを押して、関係を削除します。<br/><br/>'.
							   '関係があるキャラはないなら、何も選らなくてもいいです。後も関係を加えてもいいです。<br/><br/>'.
	                           'フィールドは編集できないなら、スタッフは関係をもう確認しました。';
	const Controls           = '「戻る」というボタンを押して、キャンセルして前のページに戻ります。<br/><br/>「出す」というボタンを押して、情報を出します。';
}

namespace Localization\AlbumEditorPage\TooltipHeading
{
	const OriginalName       = '原名';
	const TransliteratedName = 'ローマ字で書いた原名';
	const LocalizedName      = '翻訳した原名';
	const OldCover           = '今のカバー';
	const NewCover           = '新しいカバー';
	const Cover              = 'カバー';
	const VgmdbLink          = 'vgbdb.netにリンク';
	const RelatedGames       = '関係があるゲーム';
	const SongCount          = '歌の数';
	const Controls           = 'コントロール';
}

namespace Localization\AlbumEditorPage\TooltipContent
{
	const OriginalName       = 'アルバムの原名です。<br/><br/>'.
	                           'まずは、アルバムがあるかどうか、チェックしてください。<br/><br/>'.
							   '* = 必要';
	const TransliteratedName = 'ローマ字で書いた原名です。<br/><br/>'.
	                           'まずは、ローマ字に変換し方を文章作法に見てください。リンクはページの下であります。<br/><br/>'.
							   'ローマ字はＡＳＣＩＩだけを使っています。<br/><br/>'.
							   '* = 必要';
	const LocalizedName      = '外国で使った名前です。逆もまた然り：アルバムを作った国の言語に変換した名前もです。<br/><br/>'.
	                           'アルバムが外国へ出ませんでしたならば、何も書かないでください。';
	const OldCover           = '今のアルバムのカバーです。';
	const NewCover           = 'カバーを替えてほしいなら、別のイメージをアップロードしてもいいです。<br/><br/>'.
	                           '上限の大きさ:５１２キロバイト。<br/><br/>'.
							   'おすすめは、同じ幅と高さのイメージです。';
	const Cover              = 'アルバムのカバーです。<br/><br/>'.
	                           '上限の大きさ:５１２キロバイト。<br/><br/>'.
							   'おすすめは、同じ幅と高さのイメージです。';
	const VgmdbLink          = 'vgmdb.netデータベースのアルバムの情報にリンクです。<br/><br/>'.
	                           'vgmdb.netはゲームのサウンドトラックのデータベースです。たくさんの人が参加していて、情報が詳しいです。<br/><br/>'.
							   'このサイトは詳しい情報ではありません。<br/><br/>'.
							   'そして、vgmdb.netにリンクを入力してください。';
							   //'リンクを加えてアルバムを出すあと、アルバムに全ての歌を加えるようになる。';
	const RelatedGames       = 'アルバムに関係があるキャラはデータベースにあるなら、ここに選んでください。<br/><br/>'.
	                           '「＋」のボタンを押して、もう一つの関係を加えられます。<br/><br/>'.
	                           '「－」のボタンを押して、関係を削除します。<br/><br/>'.
							   '関係があるゲームはないなら、何も選らなくてもいいです。後も関係を加えてもいいです。<br/><br/>'.
	                           'フィールドは編集できないなら、スタッフは関係をもう確認しました。';
	const SongCount          = '歌の全数を入力してください。<br/><br/>'.
	                           '* = 必要';
	const Controls           = '「戻る」というボタンを押して、キャンセルして前のページに戻ります。<br/><br/>「出す」というボタンを押して、情報を出します。';
}

namespace Localization\ArtistEditorPage\TooltipHeading
{
	const OriginalName       = '自分の言語で書いた名前';
	const TransliteratedName = 'ローマ字で書いた名前';
	const LocalizedName      = '言語のルールに化した名前';
	const OldPhoto           = '今の写真';
	const NewPhoto           = '新しい写真';
	const Photo              = '写真';
	const VgmdbLink          = 'vgmdb.netにリンク';
	const OriginalArtist     = 'この通称名を使うアーチスト';
	const Controls           = 'コントロール';
}

namespace Localization\ArtistEditorPage\TooltipContent
{
	const OriginalName       = '歌手の言語で書いた名前です。<br/><br/>'.
	                           'まずは、歌手があるかどうか、チェックしてください。<br/><br/>'.
							   '* = 必要';
	const TransliteratedName = 'ローマ字で書いた名前です。<br/><br/>'.
	                           'まずは、ローマ字に変換し方を文章作法に見てください。リンクはページの下であります。<br/><br/>'.
							   'ローマ字はＡＳＣＩＩだけを使っています。<br/><br/>'.
							   '* = 必要';
	const LocalizedName      = '外国で使った名前です。逆もまた然り：歌手の国の言語に変換した名前もです。<br/><br/>'.
	                           '歌手はいつも外国に来たなら、何も書かないでください。';
	const OldPhoto           = '今の歌手の写真です。';
	const NewPhoto           = '写真を替えてほしいなら、別のイメージをアップロードしてもいいです。<br/><br/>'.
	                           '上限の大きさ:５１２キロバイト。<br/><br/>'.
							   'おすすめは、同じ幅と高さのイメージです。';
	const Photo              = '歌手の写真です。<br/><br/>'.
	                           '上限の大きさ:５１２キロバイト。<br/><br/>'.
							   'おすすめは、同じ幅と高さのイメージです。';
	const VgmdbLink          = 'vgmdb.netデータベースのアルバムの情報にリンクです。<br/><br/>'.
	                           'vgmdb.netはゲームのサウンドトラックのデータベースです。たくさんの人が参加していて、情報が詳しいです。<br/><br/>'.
							   'このサイトは詳しい情報ではありません。<br/><br/>'.
							   'そして、vgmdb.netにリンクを入力してください。';
	const OriginalArtist     = '今のアーチストは通称名なら、アーチストを選んでください。今のアーチストは通称名ではないなら、何も選らないでください。';
	const Controls           = '「戻る」というボタンを押して、キャンセルして前のページに戻ります。<br/><br/>「出す」というボタンを押して、情報を出します。';
}

namespace Localization\CharacterEditorPage\TooltipHeading
{
	const OriginalName       = '自分の言語で書いた名前';
	const TransliteratedName = 'ローマ字で書いた名前';
	const LocalizedName      = '言語のルールに化した名前';
	const OldImage           = '今のイメージ';
	const NewImage           = '新しいイメージ';
	const Image              = 'イメージ';
	const VndbLink           = 'vndb.orgにリンク';
	const RelatedGames       = '関係があるゲーム';
	const Controls           = 'コントロール';
}

namespace Localization\CharacterEditorPage\TooltipContent
{
	const OriginalName       = 'キャラの言語で書いた名前です。<br/><br/>'.
	                           'まずは、キャラがあるかどうか、チェックしてください。<br/><br/>'.
							   '* = 必要';
	const TransliteratedName = 'ローマ字で書いた名前です。<br/><br/>'.
	                           'まずは、ローマ字に変換し方を文章作法に見てください。リンクはページの下であります。<br/><br/>'.
							   'ローマ字はＡＳＣＩＩだけを使っています。<br/><br/>'.
							   '* = 必要';
	const LocalizedName      = '外国で使った名前です。逆もまた然り：キャラの国の言語に変換した名前もです。<br/><br/>'.
	                           'ゲームは外国に売られなかったなら、何も書かないでください。';
	const OldImage           = '今のキャラのイメージです。';
	const NewImage           = 'イメージを替えてほしいなら、別のイメージをアップロードしてもいいです。<br/><br/>'.
	                           '上限の大きさ:５１２キロバイト。<br/><br/>'.
							   'おすすめは、同じ幅と高さのイメージです。';
	const Image              = 'キャラのイメージです。<br/><br/>'.
	                           '上限の大きさ:５１２キロバイト。<br/><br/>'.
							   'おすすめは、同じ幅と高さのイメージです。';
	const VndbLink           = 'vndb.orgデータベースのキャラの情報にリンクです。<br/><br/>'.
	                           'vndb.orgはインターネットで一番大きいビジュアルノベルのデータベースだかもしれません。<br/><br/>'.
							   'このサイトは詳しい情報ではありません。<br/><br/>'.
							   'そして、vndb.orgにリンクを入力してください。';
	const RelatedGames       = 'キャラに関係があるキャラはデータベースにあるなら、ここに選んでください。<br/><br/>'.
	                           '「＋」のボタンを押して、もう一つの関係を加えられます。<br/><br/>'.
	                           '「－」のボタンを押して、関係を削除します。<br/><br/>'.
							   '関係があるゲームはないなら、何も選らなくてもいいです。後も関係を加えてもいいです。<br/><br/>'.
	                           'フィールドは編集できないなら、スタッフは関係をもう確認しました。';
	const Controls           = '「戻る」というボタンを押して、キャンセルして前のページに戻ります。<br/><br/>「出す」というボタンを押して、情報を出します。';
}

namespace Localization\SongEditorPage\TooltipHeading
{
	const DiscAndTrack       = 'ディスクとトラック';
	const OriginalName       = '原名';
	const TransliteratedName = 'ローマ字で書いた原名';
	const LocalizedName      = '翻訳した原名';
	const HasVocal           = '歌詞があるか？';
	const Controls           = 'コントロール';
}

namespace Localization\SongEditorPage\TooltipContent
{
	const DiscAndTrack       = 'ディスクのナンバーとトラックのナンバーです。ナンバーは自動に数えます。<br/><br/>'.
	                           '次のディスクのなら、ボタンを押してください。<br/></br>'.
	                           '* = 必要';
	const OriginalName       = '歌の原名です。<br/><br/>'.
	                           '* = 必要';
	const TransliteratedName = 'ローマ字で書いた原名です。<br/><br/>'.
	                           'まずは、ローマ字に変換し方を文章作法に見てください。リンクはページの下であります。<br/><br/>'.
							   'ローマ字はＡＳＣＩＩだけを使っています。<br/><br/>'.
							   '* = 必要';
	const LocalizedName      = '外国で使った名前です。逆もまた然り：アルバムを作った国の言語に変換した名前もです。<br/><br/>'.
	                           '歌のアルバムが外国へ出ませんでしたならば、何も書かないでください。';
	const HasVocal           = '歌はインストなら、「ない」を選んでください。歌詞があるなら、「ある」を選んでください。<br/><br/>'.
	                           '「ある」は選ばれたら、あと歌詞を加えることができるようになります。'.
							   '* = 必要';
	const Controls           = '「戻る」というボタンを押して、キャンセルして前のページに戻ります。<br/><br/>「出す」というボタンを押して、情報を出します。';
}

namespace Localization\FillAlbumPage\TooltipHeading
{
	const TrackTable = 'トラックのリスト';
	const Controls   = 'コントロール';
}

namespace Localization\FillAlbumPage\TooltipContent
{
	const TrackTable = 'vgmdb.netからもらったテーブルです。<br/></br>'.
	                   'サイトは原名とローマ字と翻訳がわかりません。そして、一の行に名前のタイプを選んでください。<br/><br/>'.
					   'ローマ字で書いた名前はあったら、文章作法のルールを守るかどうかチェックしてください。'.
					   'ローマ字で書いた名前はなかったら、自分で書いてください。<br/><br/>'.
					   '列は３つより大きく、必要がない列に選らないでください。<br/><br/>'.
					   '翻訳した名前はなき、列の名前選んでください。でも、列の下にある細胞に何も書かないで。<br/><br/>'.
					   '全ての歌に歌詞かあるかどうかと選んでください。<br/><br/>'.
					   '注意！原名とローマ字で書いて名前は必要です。<br/></br>'.
					   'ローマ字はＡＳＣＩＩだけを使っています。';
	const Controls   = '「戻る」というボタンを押して、キャンセルして前のページに戻ります。<br/><br/>「出す」というボタンを押して、情報を出します。';
}

namespace Localization\LyricsEditorPage\TooltipHeading
{
	const ArtistAndCharacter = 'カシュとキャラ';
	const OriginalSong       = '原作の歌';
	const Language           = '言語';
	const Lyrics             = '歌詞';
	const Notes              = 'コメント';
	const Controls           = 'コントロール';
}

namespace Localization\LyricsEditorPage\TooltipContent
{
	const ArtistAndCharacter = '歌手を選んでください。<br></br>'.
	                           '歌はゲームのキャラが歌ったら、キャラも選んでください。これは「ＣＶ」ということになります。<br/><br/>'.
	                           '「＋」を押して、もう一人の歌手が加えられます。<br/><br/>'.
							   '「－」を押して、歌手を外します。<br/><br/>'.
							   '* = 歌手が必要';
	const OriginalSong       = '歌はアレンジとかレミックスとか短いバージョンなどで原作を選んでもいいです。<br/><br/>'.
	                           'サイトにある歌詞と翻訳は自動に加えます。';
	const Language           = '歌詞の言語を選んでください。<br/><br/>'.
	                           'いろいろな言語があるなら、主要な言語を選んでください。<br/><br/>'.
							   '* = 必要';
	const Lyrics             = '歌詞を書いてください。<br/><br/>'.
	                           '特別な書き方が使われます。<br/><br/>'.
	                           '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                           '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                           '{nt}1{/nt}　→　歌詞とコメントに使ったら、リンクを作ります。<br/><br/>'.
							   '文章作法にもう詳しく教えます。リンクはページの下にあります。<br/><br/>'.
							   '* = 必要';
	const Notes              = '歌詞の作者とあなたのコメントがあれば、ここに書いてもいいです。<br/><br/>'.
	                           '特別な書き方が使われます。<br/><br/>'.
	                           '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                           '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                           '{nt}1{/nt}　→　歌詞とコメントに使ったら、リンクを作ります。<br/><br/>'.
							   '文章作法にもう詳しく教えます。リンクはページの下にあります。<br/><br/>';
	const Controls           = '「戻る」というボタンを押して、キャンセルして前のページに戻ります。<br/><br/>「出す」というボタンを押して、情報を出します。';
}

namespace Localization\TranslationEditorPage\TooltipHeading
{
	const TranslationLanguage = '翻訳の言語';
	const TranslationName     = '翻訳のタイトル';
	const TranslationLyrics   = '翻訳の歌詞';
	const TranslationNotes    = '翻訳のコメント';
	const Controls            = 'コントロール';
}

namespace Localization\TranslationEditorPage\TooltipContent
{
	const TranslationLanguage = '翻訳したい言語を選んでください。<br/><br/>'.
	                            '注意！あなたは同じ歌をある言語に一回だけ翻訳します。<br/><br/>'.
	                            '* = 必要';
	const TranslationName     = 'タイトルを翻訳してください。<br/><br/>'.
	                            '* = 必要';
	const TranslationLyrics   = '歌詞を翻訳してください。<br/><br/>'.
	                            '文章作法のおすすめを読まなければなりません。<br/><br/>'.
								'特別な書き方が使われます。<br/><br/>'.
								'{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
								'{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
								'{nt}1{/nt}　→　歌詞とコメントに使ったら、リンクを作ります。<br/><br/>'.
								'文章作法にもう詳しく教えます。リンクはページの下にあります。<br/><br/>'.
							    '* = 必要';
	const TranslationNotes    = '難しい言の葉を教えることと自分のコメント書くことのように使ってもいいです。<br/><br/>'.
	                            '特別な書き方が使われます。<br/><br/>'.
	                            '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                            '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                            '{nt}1{/nt}　→　歌詞とコメントに使ったら、リンクを作ります。<br/><br/>'.
							    '文章作法にもう詳しく教えます。リンクはページの下にあります。<br/><br/>';
	const Controls            = '「戻る」というボタンを押して、キャンセルして前のページに戻ります。<br/><br/>「出す」というボタンを押して、情報を出します。';
}

namespace Localization\AboutPage
{
	const Heading = 'サイトについて';
	
	const HeadingWhat = 'なにですか？';
	const What = 'このウエッブサイトはビジュアルノベルのゲームの歌のデータベースです。'.
	             'ゲームはアニメのように「opening」と「insert」と「ending」という歌があります。'.
				 'でも、別のジャンルのゲームに歌は珍しいことです。'.
				 'これこそは興味を呼び起こします。'.
				 'たくさんのゲームは日本に作られていて、アジアの外の国で歌詞と翻訳を見つけにくいです。'.
				 'その上、ビジュアルノベルはメインストリームではありません。';
	
	const HeadingWhy = 'なぜですか？';
	const Why = '誰でも音楽が好きですから、口ずさむのは珍しくないです。歌詞に興味があります。でも、どこから探しなきゃ？翻訳もどこから？'.
	            'genius.comやlyricstranslate.comなどヨーロッパに人気があります。'.
				'でも、アジアから来た歌は少ないしです。'.
				'おまけに、ゲームに関係がありません。'.
				'全部の問題を解けるため、サイトが発表しました。';
	
	const HeadingHow = 'どうやってですか？';
	const How = 'サウンドトラックはＣＤや本などを持っていますし、歌のテキストは本の中に見つかります。'.
	            '紙で作ったもので、ディジタルコピーも作ります。'.
	            'そして、サウンドトラックを買って、歌詞を書けるようになります。本はなかったら、別のサイトに探してもいい。別のサイトに何もなかったら、自分の脳と耳を使って書けます。';
	
	const HeadingWho = 'だれですか？';
	const Who = 'このウエッブサイトは自由な百科事典です。誰でも力になることができます。'.
	            'あなたも何かを書いたら、全ての世界に歌の意味を届くことができます。';
}

namespace Localization\PolicyPage
{
	const Heading = 'ウエッブサイトのポリシー';
	
	const Introduction = 'サイトは自由なデータベースです。'.
	                     '歌と歌の意味を届くように存在します。'.
	                     '広告やプロフィットはダメです。'.
	                     '買ったり、売ったり、お金を送ったりすることができません。分け合うのは大切です。';
	
	const Warning = 'あなたはウエッブサイトを使って、以下の全部のポリシーを受け入れます。';
	
	const HeadingContent  = 'コンテントポリシー';
	const ContentPolicy1  = '１．全ての貢献は公開です。誰もインターネットユーザーは見ることができます。あなたは所有権を持っていません。';
	const ContentPolicy2  = '２．イメージはインターネットから見つかります。イメージの有権者はある可能性があります。';
	const ContentPolicy3  = '３．歌詞はオフィシャルサウンドトラックと共にある本を使って書きます。聞くことでも書くことができます。'.
	                        '他のウエッブサイトのコンテントに類似している可能性があります。でも、歌詞は歌の有権者だけの物です。';
	const ContentPolicy4  = '４．全ての歌詞はウェブサイトが所有権です。誰も（ウェブサイトとウェブサイトの所有者と）利益を得ることができません。';
	const ContentPolicy5  = '５．どうでもよろしい実体は有権者が訴えを送り、消すことができます。';
	const ContentPolicy6  = '６．ウエッブサイトとウエッブサイトの所有者は何でも悪いことがあったりし、責任がありません。'.
	                        '';
	const ContentPolicy7  = '７．ユーザーはお金を稼ぐため、コンテントを使ってはいけません。';
	const ContentPolicy8  = '８．ユーザーは機械学習のため、コンテントを使ってはいけません。';
	const ContentPolicy9  = '９．ユーザーはコンテントをコピーしくて、変更しなくて、導かなくて、役に立たせなくてはいけません。、コンテントを使って、参照は必要です。';
	const ContentPolicy10 = '１０．ポリシーは変わって、誰も知らせません。';
	
	const HeadingPrivacy = 'プライバシーポリシー';
	const PrivacyPolicy1 = '１．ユーザーはウェブサイトを使るのが自己責任です。ウェブサイトは「ＡＳ　ＩＳ」というポリシーを使う。何も保証はありません。';
	const PrivacyPolicy2 = '２．全部のあなたからもらったデータは無期限に保存できます。';
	const PrivacyPolicy3 = '３．あなたについてデータは自動に集まっていて、誰にも送られていません。例外はホスティングと警察などの求めを応じします。';
	const PrivacyPolicy4 = '４．ウェブサイトはクッキー送ります。今はセッショントーケンを送って保存させます。';
	const PrivacyPolicy5 = '５．ウェブサイトはアカウントを早速に削除の機会を提供します。貢献は残っていて、「削除したユーザー」のタッグを使います。';
	const PrivacyPolicy6 = '６．ポリシーは変わって、誰も知らせません。';
	
	const HeadingRightholder = 'あなたは有権者ですなら';
	const RightholderPolicy1 = '１．あなたに情報の所有権はよろしかったら、support@vn-song-lyrics-db.ruへメールを送ってください。';
	const RightholderPolicy2 = '２．ウェブサイトは問題への解決策を見つけたいです。';
	
	const LastUpdated        = '最後の編集：２０２６年０２月１７日。';
	const Timezone           = '全部の日付：ＵＴＣ＋３。';
}

namespace Localization\RulesPage
{
	const Heading = 'サイトのルール';
	
	const HeadingGeneral = '振る舞いのルール';
	
	const GeneralRule1  = '１．ユーザーは１８歳以上の者だと見込んでいます。ウェブサイトはチェックしないだですが、ユーザーが住んでいる国は法律を持っているかもしりません。そして、自己責任です。';
	const GeneralRule2  = '２．ユーザーネームは猥褻と悪態と掃除的な意味を持っていません。';
	const GeneralRule3  = '３．ウェブサイトのテーマと違っている情報を出してはいけません。';
	const GeneralRule4  = '４．オフィシャルサウンドトラックだけを出してもいいです。「ｇａｍｅｒｉｐ」というサウンドトラックを出してはいけません。';
	const GeneralRule5  = '５．ビジュアルノベルにはたくさんのアルバムが関係するかもしれません。全部はいいです。';
	const GeneralRule6  = '６．ウェブサイトにはユーザーのコンテントを削除の権利があります。';
	const GeneralRule7a = '７．ウェブサイトには';
	const GeneralRule7b = '文章作法';
	const GeneralRule7c = 'と違っている情報を削除の権利があります。';
	const GeneralRule8  = '８．ウェブサイトはユーザーとユーザーの出す権利を限ることができます。説明とお知らせは必要ではありません。';
	
	const HeadingAccess = 'アクセスルール';
	
	const AccessRule1 = '１．サインアップしたユーザーもサインアップしなかったユーザーも訴え出ることができます。';
	const AccessRule2 = '２．サインアップしたユーザーもサインアップしなかったユーザーもウェブサイトについてコメントを出すことができます。';
	const AccessRule3 = '３．サインアップしたユーザーは何も実体を出すことができます。';
	const AccessRule4 = '４．サインアップしたユーザーは「ゲーム」と「アルバム」と「カシュ」と「キャラ」という自分の貢献を編集できて、削除できます。でも、「まだ確認されなかった」というステータスではないなら、できません。';
	const AccessRule5 = '５．「キャラ」という自分の貢献を編集できて、削除できます。でも、「まだ確認されなかった」というステートスがある場合と「ホンヤク」がある場合、できません。';
	const AccessRule6 = '６．サインアップしたユーザーは「ホンヤク」という自分の貢献を編集できて、削除できます。この場合は「まだ確認されなかった」にステータスになります。';
	const AccessRule7 = '７．ルールを守らないユーザーは無期限に限られることができます。メールを送って、訴えることができます。';
	const AccessRule8 = '８．ウェブサイトのスタッフだけステータスを変えることができます。';
}

namespace Localization\WritingGuidePage
{
	const Heading = '文章作法';
	
	const Contents         = '目次：';
	const LyricsLink       = '歌詞の書き方';
	const TranslationLink  = '翻訳の書き方';
	const RomanizationLink = 'ローマ字で書き方';
	const FormattingLink   = 'マークアップの書き方';
	const CaptchaLink      = 'キャプチャーの書き方';
	
	const HeadingLyrics       = '歌詞の書き方';
	const LyricsIntroduction1 = '歌詞の書き方は以下の通りです：';
	const LyricsIntroduction2 = '・サウンドトラックの冊子版を持っているなら、冊子版を使ってもいいです。';
	const LyricsIntroduction3 = '・別のサイトで歌詞を見つけたら、コピーできます。サイトは参照を求めているかどうかと調べてください。';
	const LyricsIntroduction4 = '・歌詞を見つかっていなかったら、聞くことで書いてもいいです。';
	const LyricsHeadsUp       = 'ルールは：';
	const LyricsRule1         = '１．全部の歌詞を書かなければなりません。「※repeat」などはダメです。';
	const LyricsRule2         = '２．歌詞をだけ書かなければなりません。「バース１」や「歌っている：歌手の名前」などのはダメです。';
	const LyricsRule3         = '３．検閲のはダメです。悪い言葉があるなら、悪い言葉も書かなければなりません。';
	const LyricsRule4         = '４．歌詞は言語のルールを守らないなら、編集はダメです。';
	const LyricsRule5         = '５．歌っている歌詞は書いてある歌詞と違っているなら、編集してもいいです。';
	const LyricsRule6         = '６．歌を聞くとき、耳をすませばいいです。あなたの結果をチェックしてください。';
	const LyricsRule7         = '７．あなたが書いている歌詞は国の法律を守るかどうかと疑いが出ると、support@vn-song-lyrics-db.ruへメールを送ってもいいです。';
	const LyricsRule8         = '８．マークアップはどうするかと見てください。';
	const LyricsRule9         = '９．「コメント」を書いてもいいです。';
	
	const HeadingTranslation = '翻訳の書き方';
	
	const TranslationIntroduction = '辞書から見つかった言葉を書くのは間違いです。';
	
	const TranslationRemember1 = '翻訳者が考えることは：';
	const TranslationRemember2 = '・文化の違い；';
	const TranslationRemember3 = '・鳴り方を守り；';
	const TranslationRemember4 = '・書き方を守り；';
	const TranslationRemember5 = '・比喩；';
	const TranslationRemember6 = '・話し方；';
	const TranslationRemember7 = '・意味を守り。';
	const TranslationRemember8 = '何かもうありますか？よかったです。';
	
	const TranslationHeadsUp = '覚えていてください：';
	const TranslationRule1   = '１．ＡＩと翻訳エンジンはダメです。自分の頭を使わなければなりません。';
	const TranslationRule2   = '２．現代の辞書を使ってもいいです。辞書は同じ言語で教えるなら、いいです。'.
	                           '翻訳をくれる辞書は古いかもしれないです。間違っているかもしれないです。';
	const TranslationRule3   = '３．言語を教えるウェブサイトを使ってもいいです。例えば：stackexchange.comやhinative.comなどです。現代の人が教えたら、いいです。';
	const TranslationRule4   = '４．例を探すのはいいです。テキスト、歌、ビデオ、全部を使ってもいいです。';
	const TranslationRule5   = '５．検索エンジンの使い方を習ってもいいです。例えば：I love youの代わりに"I love you"を入力は正確の言の葉を探します。';
	const TranslationRule6   = '６．活版の文字を使ってもいいです。例えば：「日本語のマーク」、“英語のマーク”、«ロシア語のマーク»です。';
	
	const TranslationLanguages = '注意！翻訳したい言語はないなら、コメントのページに求めてもいいです。メールを送ってもいいです。';
	
	const HeadingRomanization = 'ローマ字で書き方';
	
	const RomanizationIntroduction = 'この部は日本語のためだけです。'.
	                                 '他の言語のローマ字で書き方はあなたが選ぶことです。';
	
	const RomanizationJapanese1 = 'ヘボン式ローマ字を聞いたかもしれません。このサイトは自分のルールを使っています。'.
	                              'わかりやすくように言葉を使っていません。';
	const RomanizationJapanese2 = '注意！あなたはサインアップのとき、このルールを受け入れました。好きとか嫌いとか、受け入れました。';
	
	const HeadingAllowedSymbols = '使われる文字';
	const AllowedSymbols        = 'ＡＳＣＩＩだけです。英語のキーボード使うように書いてください。';
	
	const HeadingNameOrder = '名字と名前';
	const NameOrder        = '日本では、名字は始めで、名前は終わりです。ローマ字で書くとき、同じ並び方が使われます。';
	
	const HeadingCapitalization = '大文字の使い方';
	const Capitalization        = '名前を書くとき、助詞は小文字で始まる。助詞しかの言葉は最新の文字は大文字です。';
	
	const KanaConversionRules = 'カナの変換のルール';
	const KanaConversionNote  = 'コメント：';
	
	const KanaLanguageRules = 'カタカナ、ひらがな、漢字、ほかの言語';
	const KanaLanguageRule1 = '１．日本語の言葉小文字で書かれます。';
	const KanaLanguageRule2 = '２．日本語が借りた言葉は元の言葉に代わりません。';
	const KanaLanguageRule3 = '３．日本語しかの言語は大文字で書かれます。';
	
	const ParticlesRules      = '「離れる」とか「付ける」とか';
	const Together            = '付ける';
	const Apart               = '離れる';
	const DifferenceInMeaning = '意味は間違っているとき';
	const SpecialReadings     = '特別な読み方';
	const DivisionBySyllables = '離れた文字：';
	const OldWritingStyle     = '旧い書き方';
	
	const HeadingFormatting      = 'マークアップの書き方';
	const FormattingIntroduction = 'ウェッブサイトは書き方の機械があります：';
	const FormattingEntity1      = '・フリガナ；';
	const FormattingEntity2      = '・色づいた言葉；';
	const FormattingEntity3      = '・コメントのリンク。';
	
	const FormattingFurigana1 = '１．フリガナを書くように、書いてください：';
	const FormattingFurigana2 = '、＊＝どうでもいい文字。';
	const FormattingFurigana3 = '例：';
	const FormattingFurigana4 = '使い方：読み方が難しくて、分からなくて、特別で、珍しいです。';
	
	const FormattingColor1 = '２．色づいた言葉を書くように、書いてください：';
	const FormattingColor2 = '、XXXXXX＝１６進で書いたＲＧＢカラー、＊＝文字。';
	const FormattingColor3 = '例：';
	const FormattingColor4 = '使い方：歌手か一人じゃない場合。';
	
	const FormattingNotes1 = '３．コメントへリンクを書くように、書いてください：';
	const FormattingNotes2 = '、Ｎ＝自然数。注意：この文字列を歌詞とコメントの中に書かなければなりません。';
	const FormattingNotes3 = '例：';
	const FormattingNotes4 = '使い方：歌詞とコメントの絆。';
	
	const FormattingExample1 = 'もう詳しくほしいなら、';
	const FormattingExample2 = '歌詞の例';
	const FormattingExample3 = 'を見てください。';
	
	const FormattingWarnings = 'この機能を書くことができるの所：';
	const FormattingWarning1 = '・「歌詞」；';
	const FormattingWarning2 = '・「コメント」；';
	const FormattingWarning3 = '・「翻訳の歌詞」；';
	const FormattingWarning4 = '・「翻訳のコメント」。';
	const FormattingWarning5 = 'この機能を使うかどうか、あなたが選ぶことです。';
	
	const HeadingCaptcha = 'キャプチャーの書き方';
}

namespace Localization\LyricsExamplePage
{
	const Heading    = 'マークアップの例';
	const Formatting = 'テキスト';
	const Japanese   = '日本語';
	const Markup     = 'マークアップ';
	const Result     = '結果';
	const Notes      = 'コメント';
}

namespace Localization\ErrorPage
{
	const textBadRequest1 = '入力は無用です。';
	const textBadRequest2 = '戻って、もう一度入力をチェックしてください。';
	
	const textUnauthorized1 = '関係者以外は立入禁止です。';
	const textUnauthorized2 = 'あなたはログインしていません。';
	
	const textPaymentRequired1 = 'あなたはサーバーを払ったなのかなあ？';
	const textPaymentRequired2 = '~♪~またねえ~♪~';
	
	const textForbidden1 = '立入禁止です。';
	const textForbidden2 = 'あなたにはアクセスできません。';
	
	const textNotFound1 = 'ページが見つかりませんでした。';
	const textNotFound2 = 'ページの名前は変わったかもしれません？';
	
	const textMethodNotAllowed1 = 'サーバーはリクエストが分かりましたが、方法は許されません。';
	const textMethodNotAllowed2 = 'support@vn-song-lyrics-db.ruへメールを送ってください。';
	
	const textNotAcceptable1 = 'サーバーは求める言語をサポートしていません。';
	const textNotAcceptable2 = 'ページの上にある言語を選んでください。';
	
	const textUnavailableForLegalReasons1 = '念書が来たですから、アクセスは限られています。';
	const textUnavailableForLegalReasons2 = 'あなたにはアクセスできません。';
	
	const textInternalServerError1 = 'サーバーのエラー行いました。';
	const textInternalServerError2 = 'support@vn-song-lyrics-db.ruへメールを送ってください。';
	
	const textNotImplemented1 = '選んだ方法は知られません。';
	const textNotImplemented2 = '正しいほうでウェブサイトを使ってください。';
	
	const textBadGateway1 = 'サーバーは別のサーバーからエラーをもらいました。';
	const textBadGateway2 = 'あとで訪れてください。';
	
	const textServiceUnavailable1 = '今はウェブサイトが使用できません。';
	const textServiceUnavailable2 = 'あとで訪れてください。';
}
