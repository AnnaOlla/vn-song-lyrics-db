<?php

namespace Localization\Functions
{
	function localizeLanguageName(array $entity): string
	{
		return $entity['language_ru_name'];
	}

	function localizeLanguageKey(): string
	{
		return 'language_ru_name';
	}

	function localizeTranslationNumber(int $number): string
	{
		// Japanese only:
		// $halfWidthDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
		// $fullWidthDigits = ['０', '１', '２', '３', '４', '５', '６', '７', '８', '９'];
		//
		// return '＃'.str_replace($halfWidthDigits, $fullWidthDigits, (string)$number);
		
		return '#'.$number;
	}
	
	use InputError;
	
	function localizeInputError(InputError $error): string|null
	{
		switch ($error)
		{
			case InputError::None:
				return null;
			
			case InputError::CaptchaInvalid:
				return 'Код был введён неверно.';
			
			case InputError::EmailNotFound:
				return 'Почта не была найдена.';
			
			case InputError::EmailInvalid:
				return 'Формат адреса почты не был соблюдён.';
			
			case InputError::EmailTaken:
				return 'Данная почта не может быть использована.';
			
			case InputError::UsernameForbiddenSymbols:
				return 'Имя пользователя содержало запрещённые символы.';
			
			case InputError::UsernameLengthIncorrect:
				return 'Имя пользователя имело неверную длину.';
			
			case InputError::UsernameTaken:
				return 'Имя пользователя занято.';
			
			case InputError::IncorrectPassword:
				return 'Пароль был введён неверно.';
			
			case InputError::PasswordForbiddenSymbols:
				return 'Пароль содержал запрещённые символы.';
			
			case InputError::PasswordLengthIncorrect:
				return 'Пароль имел неверную длину.';
			
			default:
				throw new Exception(__FUNCTION__.': value '.$error->name.' was not found');
		}
	}
}

namespace Localization\HomePage
{
	const Heading          = 'Visual Novel Song Lyrics Database';
	const DescriptionOne   = 'vn-song-lyrics-db.ru стремится к тому, чтобы стать базой данных для всех песен, исполняемых в визуальных новеллах.';
	const DescriptionTwo   = 'Музыка в этом жанре игр не слишком популярна, поэтому найти слова и переводы к ней достаточно проблематично.';
	const DescriptionThree = 'Этот сайт напоминает «вики», где каждый пользователь может внести свой вклад.';
	const LastAlbums       = 'Недавно добавленные альбомы';
	const LastLyrics       = 'Недавно добавленные тексты песен';
	const LastTranslations = 'Недавно добавленные переводы песен';
}

namespace Localization\Header
{
	const HomePage        = 'vn-song-lyrics-db';
	
	const GameList        = 'Игры';
	const AlbumList       = 'Альбомы';
	const ArtistList      = 'Исполнители';
	const CharacterList   = 'Персонажи';
	const SongList        = 'Песни';
	const TranslationList = 'Переводы';
	const Feedback        = 'Отзывы';
	
	const LogIn           = 'Вход';
	const SignUp          = 'Регистрация';
	const LogOut          = 'Выход из аккаунта';
}

namespace Localization\Footer
{
	const About         = 'О сайте';
	const Policy        = 'Политика сайта';
	const BehaviorRules = 'Правила сайта';
	const WritingGuide  = 'Руководство';
}

namespace Localization\LogInPage
{
	const Heading                = 'Вход';
	
	const HintPassword           = 'При потере обратитесь к нам по почте: support@vn-song-lyrics-db.ru';
	
	const Email                  = 'Электронная почта';
	const Password               = 'Пароль';
	const Submit                 = 'Войти';
}

namespace Localization\SignUpPage
{
	const Heading  = 'Регистрация';
	
	const Username = 'Имя пользователя';
	const Password = 'Пароль';
	const Email    = 'Электронная почта';
	const Submit   = 'Зарегистрироваться';
	
	const HintUsername = 'Разрешённые символы: A-Z, a-z, 0-9. Длина: 4-32.';
	const HintEmail    = 'Используется только для верификации и входа.';
	const HintPassword = 'Разрешённые символы: A-Z, a-z, 0-9. Длина: 4-32.';
	
	const Confirmation = 'Нажимая кнопку «Зарегистрироваться», вы подтверждаете согласие с:';
	const Policy       = 'Политикой сайта';
	const Rules        = 'Правилами сайта';
	const WritingGuide = 'Руководством';
	const Warning      = 'Убедительно просим уделить пару минут на ознакомление.';
	
	const AwaitingVerification = 'На указанную почту отправлено письмо. Пожалуйста, проверьте входящие письма, а также папку со спамом.';
}

namespace Localization\GameListPage
{
	const Heading = 'Список игр';
	const AddGame = 'Добавить игру';
}

namespace Localization\AlbumListPage
{
	const Heading  = 'Список альбомов';
	const AddAlbum = 'Добавить альбом';
}

namespace Localization\ArtistListPage
{
	const Heading   = 'Список исполнителей';
	const AddArtist = 'Добавить исполнителя';
}

namespace Localization\CharacterListPage
{
	const Heading      = 'Список персонажей';
	const AddCharacter = 'Добавить персонажа';
}

namespace Localization\SongListPage
{
	const Heading     = 'Список песен';
	const SongName    = 'Название песни';
}

namespace Localization\TranslationListPage
{
	const Heading = 'Список переводов';
}

namespace Localization\FeedbackPage
{
	const Heading             = 'Отзывы';
	
	const Introduction        = 'На этой странице вы можете оставить пару слов о сайте.';
	const MessagePublic       = 'Все написанные отзывы являются публичными.';
	const AboutAnswer         = 'Администрация может оставить ответ на отзыв.';
	const SymbolLimit         = 'Длина сообщения: не более 500 символов.';
	
	const TextareaPlaceholder = 'Текст отзыва';
	const AnonymousAuthor     = 'Аноним';
	const ReplyFromStaff      = 'Администрация ';
	const Submit              = 'Отправить';
	
	const Delete              = 'Удалить';
	const SendReply           = 'Отправить/Заменить';
}

namespace Localization\GamePage
{
	const Details           = 'Подробности: ';
	const RelatedCharacters = 'Связанные с игрой персонажи';
	const RelatedAlbums     = 'Связанные с игрой альбомы';
}

namespace Localization\AlbumPage
{
	const Details      = 'Подробности: ';
	const SongCount    = 'Количество треков: ';
	
	const SongList     = 'Треклист';
	const NoSongsAdded = 'В альбом ещё не добавлено ни одного трека.';
	const RelatedGames = 'Связанные с альбомом игры';
	
	const DiscNumber   = 'Диск';
	const TrackNumber  = 'Трек';
	const SongName     = 'Название';
	
	const AddSong      = 'Добавить трек';
	const EditSong     = 'Редактировать';
	const FillAlbum    = 'Заполнить с vgmdb.net';
}

namespace Localization\ArtistPage
{
	const Details      = 'Подробности: ';
	const AliasOf      = 'Является псевдонимом исполнителя: ';
	const Aliases      = 'Псевдонимы';
	const RelatedSongs = 'Связанные с исполнителем песни';
}

namespace Localization\CharacterPage
{
	const Details      = 'Подробности: ';
	const RelatedGames = 'Связанные с персонажем игры';
	const RelatedSongs = 'Связанные с персонажем песни';
}

namespace Localization\SongPage
{
	// nothing here yet
}

namespace Localization\LyricsPage
{
	const LyricsHeadingStart          = 'Слова песни «';
	const LyricsHeadingEnd            = '»';
	
	const TranslationHeadingStart     = 'Перевод песни «';
	const TranslationHeadingMiddle    = '» (';
	const TranslationHeadingEnd       = ')';
	
	const NoLyricsAdded               = 'Слова ещё не были добавлены.';
	const AddLyrics                   = 'Добавить текст';
	const EditLyrics                  = 'Редактировать текст';
	const DeleteLyrics                = 'Удалить текст';
	const ReportLyrics                = 'Пожаловаться на текст';
	
	const AddLyricsUnavailableNoVoice = 'Этот трек является инструментальным.';
	const AddLyricsUnavailableForCopy = 'Но можно написать слова к оригинальной песне.';
	
	const AddTranslation              = 'Добавить перевод';
	const EditTranslation             = 'Редактировать перевод';
	const DeleteTranslation           = 'Удалить перевод';
	const ReportTranslation           = 'Пожаловаться на перевод';
	
	const LyricsOf                    = 'Слова песни ';
	const LyricsNotes                 = 'Заметки';
	const LyricsNoNotes               = 'Заметок нет.';
	
	const TranslationOf               = 'Перевод песни ';
	const TranslationNotes            = 'Заметки';
	const TranslationNoNotes          = 'Заметок нет.';
	
	const OriginalSong                = 'Оригинал: ';
	const Album                       = 'Альбом: ';
	const ShowLyricsOnly              = 'Песня: ';
	const TranslationList             = 'Переводы: ';
	const PerformerList               = 'Исполнители: ';
	
	const ListElementSeparator        = ', ';
	const CvOpeningBracket            = ' (CV. ';
	const CvClosingBracket            = ')';
}

namespace Localization\GameEditorPage
{
	const HeadingAdd         = 'Добавить игру';
	const HeadingEdit        = 'Редактировать игру: ';
	
	const OriginalName       = 'Оригинальное название';
	const TransliteratedName = 'Романизация названия';
	const LocalizedName      = 'Локализованное название';
	const VndbLink           = 'Ссылка на игру на vndb.org';
	const UploadNewLogo      = 'Загрузить новый логотип';
	const OldLogo            = 'Текущий логотип';
	const NewLogo            = 'Новый логотип';
	const Logo               = 'Логотип';
	const RelatedAlbums      = 'Связанные с игрой альбомы';
	const RelatedCharacters  = 'Связанные с игрой персонажи';
}

namespace Localization\AlbumEditorPage
{
	const HeadingAdd         = 'Добавить альбом';
	const HeadingEdit        = 'Редактировать альбом: ';
	
	const OriginalName       = 'Оригинальное название';
	const TransliteratedName = 'Романизация названия';
	const LocalizedName      = 'Локализованное название';
	const VgmdbLink          = 'Ссылка на альбом на vgmdb.net';
	const UploadNewCover     = 'Загрузить новую обложку альбома';
	const OldCover           = 'Текущая обложка альбома';
	const NewCover           = 'Новая обложка альбома';
	const Cover              = 'Обложка альбома';
	const RelatedGames       = 'Связанные с альбомом игры';
	const SongCount          = 'Количество треков';
}

namespace Localization\ArtistEditorPage
{
	const HeadingAdd         = 'Добавить исполнителя';
	const HeadingEdit        = 'Редактировать исполнителя: ';
	
	const OriginalName       = 'Имя на родном языке';
	const TransliteratedName = 'Романизация имени';
	const LocalizedName      = 'Локализованное имя';
	const VgmdbLink          = 'Ссылка на исполнителя на vgmdb.net';
	const UploadNewPhoto     = 'Загрузить новую фотографию';
	const OldPhoto           = 'Текущая фотография';
	const NewPhoto           = 'Новая фотография';
	const Photo              = 'Фотография';
	const OriginalArtist     = 'Является псевдонимом исполнителя';
}

namespace Localization\CharacterEditorPage
{
	const HeadingAdd         = 'Добавить персонажа';
	const HeadingEdit        = 'Редактировать персонажа: ';
	
	const OriginalName       = 'Имя на родном языке';
	const TransliteratedName = 'Романизация имени';
	const LocalizedName      = 'Локализованное имя';
	const VndbLink           = 'Ссылка на персонажа на vndb.org';
	const UploadNewImage     = 'Загрузить новое изображение';
	const OldImage           = 'Текущее изображение';
	const NewImage           = 'Новое изображение';
	const Image              = 'Изображение';
	const RelatedGames       = 'Связанные с персонажем игры';
}

namespace Localization\SongEditorPage
{
	const HeadingAdd         = ': добавить трек';
	const HeadingEdit        = 'Редактировать трек: ';
	
	const NextDisc           = 'Диск: +1';
	const PreviousDisc       = 'Диск: −1';
	const DiscAndTrack       = 'Диск и трек';
	const DiscNumber         = 'Диск';
	const TrackNumber        = 'Трек';
	const OriginalName       = 'Оригинальное название';
	const TransliteratedName = 'Романизация названия';
	const LocalizedName      = 'Локализованное название';
	const HasVocal           = 'Есть слова?';
	const HasVocalTrue       = 'Да';
	const HasVocalFalse      = 'Нет';
	
	const SubmitLastSong     = 'Добавить последний трек';
	const SubmitNonLastSong  = 'Добавить и перейти к следующему треку';
	const SubmitChanges      = 'Подтвердить изменения';
}

namespace Localization\LyricsEditorPage
{
	const HeadingAdd         = 'Добавить текст песни: ';
	const HeadingEdit        = 'Редактировать текст песни: ';
	const ArtistAndCharacter = 'Исполнитель и персонаж';
	const PerformsAs         = 'в роли';
	const OriginalSong       = 'Оригинал песни';
	const Language           = 'Язык';
	const Lyrics             = 'Текст';
	const Notes              = 'Заметки';
}

namespace Localization\TranslationEditorPage
{
	const HeadingAdd        = 'Добавить перевод песни';
	const HeadingEdit       = 'Редактировать перевод песни';
	
	const SourceLanguage    = 'Язык исполнения';
	const TargetLanguage    = 'Язык перевода';
	const SongName          = 'Изначальное название';
	const TranslationName   = 'Перевод названия';
	const SongLyrics        = 'Текст песни';
	const TranslationLyrics = 'Перевод текста песни';
	const SongNotes         = 'Заметки';
	const TranslationNotes  = 'Заметки переводчика';
}

namespace Localization\FillAlbumEditorPage
{
	const Heading            = 'Заполнить альбом: ';
	
	const DiscNumber         = 'Диск';
	const TrackNumber        = 'Трек';
	const OriginalName       = 'Оригинальное название';
	const TransliteratedName = 'Романизация названия';
	const LocalizedName      = 'Локализованное название';
	
	const HasVocal           = 'Есть слова?';
	const HasVocalTrue       = 'Да';
	const HasVocalFalse      = 'Нет';
}

namespace Localization\DeleteEntityPage
{
	const DeleteGame        = 'Удалить игру: ';
	const DeleteAlbum       = 'Удалить альбом: ';
	const DeleteArtist      = 'Удалить исполнителя: ';
	const DeleteCharacter   = 'Удалить персонажа: ';
	const DeleteSong        = 'Удалить трек: ';
	const DeleteLyrics      = 'Удалить слова: ';
	const DeleteTranslation = 'Удалить перевод: ';
	
	const Game              = 'Игра';
	const Album             = 'Альбом';
	const Artist            = 'Исполнитель';
	const Character         = 'Персонаж';
	const Song              = 'Трек';
	const Lyrics            = 'Текст песни';
	const Translation       = 'Перевод песни';
	
	const Introduction      = 'Вы уверены, что хотите удалить эту информацию?';
	const Warning           = 'Это действие невозможно отменить.';
}

namespace Localization\ReportPage
{
	const Heading            = 'Жалоба: ';
	
	const Introduction       = 'На этой странице вы можете подать анонимную жалобу администрации сайта на контент.';
	const AboutReportContent = 'По всей видимости, вы нашли неполную, неверную или неприемлемую информацию. Пожалуйста, расскажите подробно о проблеме.';
	const NoActionWarning    = 'Просим заметить, что реакция администрации не мгновенная, более того, ваша жалоба может быть отклонена.';
	const ReplyOpportunity   = 'Если вы хотите получить ответ от администрации, то подайте жалобу нам на почту support@vn-song-lyrics-db.ru.';
	const SymbolLimitWarning = 'Сообщение ограничено 250 символами.';
	const Redirect           = 'После оформления вы будете перенаправлены на предыдущую страницу.';
	
	const Game               = ' (игра)';
	const Album              = ' (альбом)';
	const Artist             = ' (исполнитель)';
	const Character          = ' (персонаж)';
	const Song               = ' (песня)';
	const Lyrics             = ' (текст песни)';
	const Translation        = ' (перевод песни)';
}

namespace Localization\UserPage
{
	const User                 = 'Пользователь: ';
	const Role                 = 'Роль: ';
	
	const AccountControl       = 'Управление аккаунтом';
	const ChangeAccountData    = 'Изменить данные';
	const DeleteAccount        = 'Удалить аккаунт';
	
	const Contributions        = 'Внесённый вклад';
	const RelatedGames         = 'Игры: ';
	const RelatedAlbums        = 'Альбомы: ';
	const RelatedArtists       = 'Исполнители: ';
	const RelatedCharacters    = 'Персонажи: ';
	const RelatedSongs         = 'Песни: ';
	const RelatedTranslations  = 'Переводы: ';
}

namespace Localization\UserAccountDataPage
{
	const Edit            = ': изменение';
	const AccountData     = 'Данные аккаунта';
	
	const Username        = 'Имя пользователя';
	const Email           = 'Электронная почта';
	const NewPassword     = 'Новый пароль';
	const NewPasswordNote = 'Заполнить только при необходимости смены';
	const OldPassword     = 'Текущий пароль';
}

namespace Localization\UserAccountDeletePage
{
	const Delete       = ': удалить';
	const AccountData  = 'Предупреждение';
	
	const Warning1     = 'Вы находитесь на странице удаления аккаунта';
	const Warning2     = 'В результате сайт забудет ваши данные, например, электронную почту.';
	const Warning3     = 'Весь ваш вклад останется, но авторство будет потеряно: автором станет «Удалённый пользователь».';
	const Warning4     = 'Отменить действие невозможно. Аккаунт удаляется немедленно.';
	const Confirmation = 'Для подтверждения введите пароль от аккаунта.';
	
	const Password     = 'Пароль';
}

namespace Localization\ImageAltText
{
	const NoGameLogo       = 'Нет логотипа игры';
	const NoAlbumCover     = 'Нет обложки альбома';
	const NoArtistPhoto    = 'Нет фотографии исполнителя';
	const NoCharacterImage = 'Нет изображения персонажа';
	
	const GameLogoOf       = 'Логотип игры ';
	const AlbumCoverOf     = 'Обложка альбома ';
	const ArtistPhotoOf    = 'Фотография исполнителя ';
	const CharacterImageOf = 'Изображение персонажа ';
}

namespace Localization\TimestampString
{
	const Added       = 'Добавлено';
	const Updated     = 'Обновлено';
	const Reviewed    = 'Рассмотрено';
	const By          = ' пользователем ';
	const DeletedUser = '«Удалённый пользователь»';
	const Delimeter   = ' ';
}

namespace Localization\ModerationStatus
{
	const RelationStatus = 'Статус связи: ';
	const Status         = 'Статус: ';
	const Unchecked      = 'Ожидает подтверждения администрацией';
	const Checked        = 'Подтверждено';
	const Hidden         = 'Скрыто';
	const Unknown        = 'Неизвестно';
}

namespace Localization\Tooltip
{
	const UserVisitor         = 'Сначала нужно войти или зарегистрироваться';
	const UserViolator        = 'Ваш аккаунт был ограничен в праве изменения данных';
	const UserNotAuthor       = 'Вы не являетесь участником, добавившим эту информацию';
	const NotOriginalSong     = 'Перейдите к оригинальной песне и узнайте, можете ли вы редактировать её';
	const InfoHidden          = 'Информация скрыта администрацией по формальным причинам';
	const InfoChecked         = 'Информация проверена администрацией';
	const SongHasTranslations = 'У песни есть переводы';
	const OriginalLanguage    = 'Нельзя сделать перевод песни на язык её исполнения';
	const AlreadyTranslated   = 'Вы уже переводили данную песню на этот язык';
}

namespace Localization\Controls
{
	const Report       = 'Пожаловаться на эту страницу';
	const Edit         = 'Редактировать информацию';
	const Delete       = 'Удалить информацию';
	
	const SearchHeading     = 'Поиск по всей базе данных';
	const SearchPlaceholder = 'Введите текст …';
	const SearchButton      = 'Поиск';
	const PageHeading       = 'Страница';
	const LimitHeading      = 'Число записей на странице';
	const NoLimit           = 'Все';
	
	const FilterPage   = 'Фильтровать записи на этой странице …';
	const Textarea     = 'Введите текст …';
	
	const ChooseFile   = 'Выбрать файл (максимальный размер: 512 KiB) …';
	const FileTooBig   = 'Файл слишком большой. Попробуйте другой файл …';
	
	const Cancel       = 'Назад';
	const Confirmation = 'Подтверждаю';
	const Submit       = 'Отправить';
}

namespace Localization\TooltipWindow
{
	const DefaultHeading = 'Подсказка';
	const DefaultContent = 'Здесь вы можете увидеть подсказки об элементах страницы.'.
							'</br></br>Наведите курсор на любое поле, чтобы узнать подробности.';
}

namespace Localization\GameEditorPage\TooltipHeading
{
	const OriginalName       = 'Оригинальное название';
	const TransliteratedName = 'Романизация названия';
	const LocalizedName      = 'Локализованное название';
	const OldLogo            = 'Текущий логотип';
	const NewLogo            = 'Новый логотип';
	const VndbLink           = 'Ссылка на игру на vndb.org';
	const Logo               = 'Логотип';
	const RelatedAlbums      = 'Связанные с игрой альбомы';
	const RelatedCharacters  = 'Связанные с игрой персонажи';
	const Controls           = 'Управление';
}

namespace Localization\GameEditorPage\TooltipContent
{
	const OriginalName       = 'Оригинальное название игры.<br/><br/>'.
	                           'Просим сначала проверить, присутствует ли игра в базе данных.<br/><br/>'.
							   '* = обязательно';
	const TransliteratedName = 'Романизация названия.<br/><br/>'.
	                           'Просим взглянуть на правила романизации в руководстве. Оно находится в самом низу страницы.<br/><br/>'.
							   'Отдельно стоит отметить то, что можно использовать только символы ASCII.<br/><br/>'.
							   '* = обязательно';
	const LocalizedName      = 'Название, адаптированное либо для международного рынка, либо для своего собственного.<br/><br/>'.
	                           'Не требуется заполнять, если игра не была официально выпущена на иностранных площадкках.';
	const OldLogo            = 'Текущий логотип игры.';
	const NewLogo            = 'Если хотите заменить логотип, загрузите новый.<br/><br/>'.
	                           'Максимальный размер изображения: 512 килобайт.<br/><br/>'.
							   'Рекомендуется использовать квадратное изображение.';
	const Logo               = 'Логотип игры.<br/><br/>'.
	                           'Максимальный размер изображения: 512 килобайт.<br/><br/>'.
							   'Рекомендуется использовать квадратное изображение.';
	const VndbLink           = 'Ссылка на игру в базе данных vndb.org.<br/><br/>'.
	                           'vndb.org — это большая и подробная база данных о визуальных новеллах.<br/><br/>'.
							   'Нет никакого смысла дублировать информацию оттуда.<br/><br/>'.
							   'Поэтому, просим просто указать ссылку на подробности с этого сайта.';
	const RelatedAlbums      = 'Если какие-то из уже добавленных альбомов имеют отношение к этой игре, укажите это здесь.<br/><br/>'.
	                           'Нажмите «плюс», чтобы добавить ещё один альбом.<br/><br/>'.
	                           'Нажмите «минус», чтобы удалить альбом.<br/><br/>'.
							   'Если на данный момент выбрать нечего, то эти связи можно будет добавить позже.<br/><br/>'.
	                           'Если поле недоступно для редактирования, то это значит, что связь утверждена администрацией.';
	const RelatedCharacters  = 'Если какие-то уже добавленные персонажи имеют отношение к этой игре, то укажите это здесь.<br/><br/>'.
	                           'Нажмите «плюс», чтобы добавить ещё одного персонажа.<br/><br/>'.
	                           'Нажмите «минус», чтобы удалить персонажа.<br/><br/>'.
							   'Если на данный момент выбрать нечего, то эти связи можно будет добавить позже.<br/><br/>'.
	                           'Если поле недоступно для редактирования, то это значит, что связь утверждена администрацией.';
	const Controls           = 'Нажмите «Отменить», чтобы вернуться назад.<br/><br/>Нажмите «Отправить», чтобы сохранить изменения.';
}

namespace Localization\AlbumEditorPage\TooltipHeading
{
	const OriginalName       = 'Оригинальное название';
	const TransliteratedName = 'Романизация названия';
	const LocalizedName      = 'Локализованное название';
	const OldCover           = 'Текущая обложка альбома';
	const NewCover           = 'Новая обложка альбома';
	const Cover              = 'Обложка альбома';
	const VgmdbLink          = 'Ссылка на альбом на vgmdb.net';
	const RelatedGames       = 'Связанные с альбомом игры';
	const SongCount          = 'Количество треков';
	const Controls           = 'Управление';
}

namespace Localization\AlbumEditorPage\TooltipContent
{
	const OriginalName       = 'Оригинальное название альбома.<br/><br/>'.
	                           'Просим сначала проверить, присутствует ли альбом в базе данных.<br/><br/>'.
							   '* = обязательно';
	const TransliteratedName = 'Романизация названия.<br/><br/>'.
	                           'Просим взглянуть на правила романизации в руководстве. Оно находится в самом низу страницы.<br/><br/>'.
							   'Отдельно стоит отметить то, что можно использовать только символы ASCII.<br/><br/>'.
							   '* = обязательно';
	const LocalizedName      = 'Название, адаптированное либо для международного рынка, либо для своего собственного.<br/><br/>'.
	                           'Не требуется заполнять, если альбом не был официально выпущен на иностранных площадкках.';
	const OldCover           = 'Текущая обложка альбома.';
	const NewCover           = 'Если хотите заменить обложку, загрузите новую.<br/><br/>'.
	                           'Максимальный размер изображения: 512 килобайт.<br/><br/>'.
							   'Рекомендуется использовать квадратное изображение.';
	const Cover              = 'Обложка альбома.<br/><br/>'.
	                           'Максимальный размер изображения: 512 килобайт.<br/><br/>'.
							   'Рекомендуется использовать квадратное изображение.';
	const VgmdbLink          = 'Ссылка на альбом в базе данных vgmdb.net.<br/><br/>'.
	                           'vgmdb.net — это база данных для официальных саундтреков из всех жанров игр, которая поддерживается большим сообществом.<br/><br/>'.
							   'Нет никакого смысла дублировать информацию оттуда.<br/><br/>'.
							   'Поэтому, просим просто указать ссылку на подробности с этого сайта.';
							   //'Заполнив это поле, можно будет в полуавтоматическом режиме заполнить альбом информацией о треках позже.';
	const RelatedGames       = 'Если какие-то из уже добавленных игр имеют отношение к этому альбому, укажите это здесь.<br/><br/>'.
	                           'Нажмите «плюс», чтобы добавить ещё одну игру.<br/><br/>'.
	                           'Нажмите «минус», чтобы удалить игру.<br/><br/>'.
							   'Если на данный момент выбрать нечего, то эти связи можно будет добавить позже.<br/><br/>'.
	                           'Если поле недоступно для редактирования, то это значит, что связь утверждена администрацией.';
	const SongCount          = 'Введите общее число треков на всех дисках альбома.<br/><br/>'.
	                           '* = обязательно';
	const Controls           = 'Нажмите «Отменить», чтобы вернуться назад.<br/><br/>Нажмите «Отправить», чтобы сохранить изменения.';
}

namespace Localization\ArtistEditorPage\TooltipHeading
{
	const OriginalName       = 'Имя на родном языке';
	const TransliteratedName = 'Романизация имени';
	const LocalizedName      = 'Локализованное имя';
	const OldPhoto           = 'Текущая фотография';
	const NewPhoto           = 'Новая фотография';
	const Photo              = 'Фотография';
	const VgmdbLink          = 'Ссылка на исполнителя на vgmdb.net';
	const OriginalArtist     = 'Является псевдонимом исполнителя';
	const Controls           = 'Управление';
}

namespace Localization\ArtistEditorPage\TooltipContent
{
	const OriginalName       = 'Имя исполнителя на его родном языке.<br/><br/>'.
	                           'Просим сначала проверить, присутствует ли исполнитель в базе данных.<br/><br/>'.
							   '* = обязательно';
	const TransliteratedName = 'Романизация имени.<br/><br/>'.
	                           'Просим взглянуть на правила романизации в руководстве. Оно находится в самом низу страницы.<br/><br/>'.
							   'Отдельно стоит отметить то, что можно использовать только символы ASCII.<br/><br/>'.
							   '* = обязательно';
	const LocalizedName      = 'Имя, адаптированное либо для международного рынка, либо для своего собственного.<br/><br/>'.
	                           'Не требуется заполнять, если работы исполнителя не были официально выпущены на иностранных площадкках.';
	const OldPhoto           = 'Текущая фотография';
	const NewPhoto           = 'Если хотите заменить фотографию, загрузите новую.<br/><br/>'.
	                           'Максимальный размер изображения: 512 килобайт.<br/><br/>'.
							   'Рекомендуется использовать квадратное изображение.';
	const Photo              = 'Фотография исполнителя.<br/><br/>'.
	                           'Максимальный размер изображения: 512 килобайт.<br/><br/>'.
							   'Рекомендуется использовать квадратное изображение.';
	const VgmdbLink          = 'Ссылка на исполнителя в базе данных vgmdb.net.<br/><br/>'.
	                           'vgmdb.net — это база данных для официальных саундтреков из всех жанров игр, которая поддерживается большим сообществом.<br/><br/>'.
							   'Нет никакого смысла дублировать информацию оттуда.<br/><br/>'.
							   'Поэтому, просим просто указать ссылку на подробности с этого сайта.';
	const OriginalArtist     = 'Если текущий исполнитель является псевдонимом другого, то укажите это здесь. Иначе, оставьте поле пустым.';
	const Controls           = 'Нажмите «Отменить», чтобы вернуться назад.<br/><br/>Нажмите «Отправить», чтобы сохранить изменения.';
}

namespace Localization\CharacterEditorPage\TooltipHeading
{
	const OriginalName       = 'Имя на родном языке';
	const TransliteratedName = 'Романизация имени';
	const LocalizedName      = 'Локализованное имя';
	const OldImage           = 'Текущее изображение';
	const NewImage           = 'Новое изображение';
	const Image              = 'Изображение';
	const VndbLink           = 'Ссылка на персонажа на vndb.org';
	const RelatedGames       = 'Связанные с персонажем игры';
	const Controls           = 'Управление';
}

namespace Localization\CharacterEditorPage\TooltipContent
{
	const OriginalName       = 'Имя исполнителя на его родном языке.<br/><br/>'.
	                           'Просим сначала проверить, присутствует ли персонаж в базе данных.<br/><br/>'.
							   '* = обязательно';
	const TransliteratedName = 'Романизация имени.<br/><br/>'.
	                           'Просим взглянуть на правила романизации в руководстве. Оно находится в самом низу страницы.<br/><br/>'.
							   'Отдельно стоит отметить то, что можно использовать только символы ASCII.<br/><br/>'.
							   '* = обязательно';
	const LocalizedName      = 'Имя, адаптированное либо для международного рынка, либо для своего собственного.<br/><br/>'.
	                           'Не требуется заполнять, если игра, в которой представлен персонаж, не была официально выпущена на иностранных площадкках.';
	const OldImage           = 'Текущее изображение персонажа.';
	const NewImage           = 'Если хотите заменить изображение, загрузите новое.<br/><br/>'.
	                           'Максимальный размер изображения: 512 килобайт.<br/><br/>'.
							   'Рекомендуется использовать квадратное изображение.';
	const Image              = 'Изображение персонажа.<br/><br/>'.
	                           'Максимальный размер изображения: 512 килобайт.<br/><br/>'.
							   'Рекомендуется использовать квадратное изображение.';
	const VndbLink           = 'Ссылка на персонажа в базе данных vndb.org.<br/><br/>'.
	                           'vndb.org — это большая и подробная база данных о визуальных новеллах.<br/><br/>'.
							   'Нет никакого смысла дублировать информацию оттуда.<br/><br/>'.
							   'Поэтому, просим просто указать ссылку на подробности с этого сайта.';
	const RelatedGames       = 'Если какие-то из уже добавленных игр имеют отношение к этому персонажу, укажите это здесь.<br/><br/>'.
	                           'Нажмите «плюс», чтобы добавить ещё одну игру.<br/><br/>'.
	                           'Нажмите «минус», чтобы удалить игру.<br/><br/>'.
							   'Если на данный момент выбрать нечего, то эти связи можно будет добавить позже.<br/><br/>'.
	                           'Если поле недоступно для редактирования, то это значит, что связь утверждена администрацией.';
	const Controls           = 'Нажмите «Отменить», чтобы вернуться назад.<br/><br/>Нажмите «Отправить», чтобы сохранить изменения.';
}

namespace Localization\SongEditorPage\TooltipHeading
{
	const DiscAndTrack       = 'Диск и трек';
	const OriginalName       = 'Оригинальное название';
	const TransliteratedName = 'Романизация названия';
	const LocalizedName      = 'Локализованное название';
	const HasVocal           = 'Есть слова?';
	const Controls           = 'Управление';
}

namespace Localization\SongEditorPage\TooltipContent
{
	const DiscAndTrack       = 'Номер диска и номер трека. Счёт ведётся автоматически.<br/></br/>'.
	                           'Если нужно начать новый диск, то используйте кнопку.<br/><br/>'.
	                           '* = обязательно';
	const OriginalName       = 'Оригинальное название.<br/><br/>'.
	                           '* = обязательно';
	const TransliteratedName = 'Романизация названия.<br/><br/>'.
	                           'Просим взглянуть на правила романизации в руководстве. Оно находится в самом низу страницы.<br/><br/>'.
							   'Отдельно стоит отметить то, что можно использовать только символы ASCII.<br/><br/>'.
							   '* = обязательно';
	const LocalizedName      = 'Название, адаптированное либо для международного рынка, либо для своего собственного.<br/><br/>'.
	                           'Если альбом с этим треком не выпускался для иностранных рынков, оставьте это поле пустым.';
	const HasVocal           = 'Если трек — инструментальный, выберите «Нет». Иначе укажите «Да». <br/><br/>'.
	                           'Выбрав «Да», вы сможете позже добавить слова, найдя песню в списке треков альбома.'.
	                           '* = обязательно';
	const Controls           = 'Нажмите «Отменить», чтобы вернуться назад.<br/><br/>Нажмите «Отправить», чтобы сохранить изменения.';
}

namespace Localization\FillAlbumPage\TooltipHeading
{
	const TrackTable = 'Список треков';
	const Controls   = 'Управление';
}

namespace Localization\FillAlbumPage\TooltipContent
{
	const TrackTable = 'Таблица сформирована на основе данных, полученных из vgmdb.net.<br/></br>'.
	                   'К сожалению, сайт не способен сам определить, какие из названий являются оригинальными, переведёнными и романизованными. Необходимо расставить заголовки в первой строке.<br/><br/>'.
					   'Правила романизации можно найти в нижней панели сайта, проверьте, что названия соответствуют им.'.
					   'Если романизации нет, то напишите её в свободный столбец самостоятельно.<br/><br/>'.
					   'Если в таблице больше столбцов, чем необходимо (3), не маркируйте оставшиеся.<br/><br/>'.
					   'Если названия не переводились на другие языки, то поставьте ему заголовок, но оставьте все значения под ним пустыми.<br/><br/>'.
					   'Также для каждого трека нужно указать, есть ли у него слова или нет.<br/><br/>'.
					   'В таблице обазательно должны присутствовать столбцы с оригинальным названием и романизацией, а ячейки под ними заполненными.<br/></br>'.
					   'Отдельно стоит отметить то, что в романизации можно использовать только символы ASCII.';
	const Controls   = 'Нажмите «Отменить», чтобы вернуться назад.<br/><br/>Нажмите «Отправить», чтобы сохранить изменения.';
}

namespace Localization\LyricsEditorPage\TooltipHeading
{
	const ArtistAndCharacter = 'Исполнитель и персонаж';
	const OriginalSong       = 'Оригинал песни';
	const Language           = 'Язык исполнения';
	const Lyrics             = 'Слова';
	const Notes              = 'Заметки';
	const Controls           = 'Управление';
}

namespace Localization\LyricsEditorPage\TooltipContent
{
	const ArtistAndCharacter = 'Укажите исполнителей песни.<br></br>'.
	                           'Если песню исполняется от имени персонажа (явление известно как Character Voice), то укажите персонажа тоже.<br/><br/>'.
	                           'Нажмите «плюс», чтобы добавить ещё одного исполнителя.<br/><br/>'.
	                           'Нажмите «минус», чтобы удалить исполнителя.<br/><br/>'.
							   '* = обязательно указать исполнителей (но не персонажей)';
	const OriginalSong       = 'Если данная песня является ремиксом, исполнена в другой аранжировке или урезана для игры, то укажите оригинал песни.<br/><br/>'.
	                           'Слова и доступные переводы в таком случае будут перенесены в эту песню автоматически.';
	const Language           = 'Укажите язык исполнения песни.<br/><br/>'.
	                           'В некоторых песнях могут встречаться фразы на других языках. В таком случае укажите преобладающий язык .<br/><br/>'.
							   '* = обязательно';
	const Lyrics             = 'Напишите слова песни.<br/><br/>'.
	                           'Здесь можно использовать специальный синтаксис, чтобы сделать текст более понятным.<br/><br/>'.
	                           '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                           '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                           '{nt}1{/nt}　→　Если указать строку такого вида и в словах, и в заметках, то между ними будут рабочие ссылки.<br/><br/>'.
							   'Подробности использования можно найти в руководстве. Оно находится в самой нижней панели сайта.<br/><br/>'.
							   '* = обязательно';
	const Notes              = 'Напишите заметки и комментарии к тексту, если это необходимо.<br/><br/>'.
	                           'Здесь можно использовать специальный синтаксис, чтобы сделать текст более понятным.<br/><br/>'.
	                           '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                           '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                           '{nt}1{/nt}　→　Если указать строку такого вида и в словах, и в заметках, то между ними будут рабочие ссылки.<br/><br/>'.
							   'Подробности использования можно найти в руководстве. Оно находится в самой нижней панели сайта.<br/><br/>';
	const Controls           = 'Нажмите «Отменить», чтобы вернуться назад.<br/><br/>Нажмите «Отправить», чтобы сохранить изменения.';
}

namespace Localization\TranslationEditorPage\TooltipHeading
{
	const TranslationLanguage = 'Язык';
	const TranslationName     = 'Название';
	const TranslationLyrics   = 'Слова';
	const TranslationNotes    = 'Заметки';
	const Controls            = 'Управление';
}

namespace Localization\TranslationEditorPage\TooltipContent
{
	const TranslationLanguage = 'Выберите язык, на который желаете сделать перевод песни.<br/><br/>'.
	                            'Одну и ту же песню нельзя переводить на тот же самый язык несколько раз одним пользователем.<br/><br/>'.
	                            '* = обязательно';
	const TranslationName     = 'Переведите название песни.<br/><br/>'.
	                            '* = обязательно';
	const TranslationLyrics   = 'Переведите слова песни.<br/><br/>'.
	                            'Просим прочитать рекомендации в руководстве.<br/><br/>'.
								'Здесь можно использовать специальный синтаксис, чтобы сделать текст более понятным.<br/><br/>'.
	                            '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                            '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                            '{nt}1{/nt}　→　Если указать строку такого вида и в словах, и в заметках, то между ними будут рабочие ссылки.<br/><br/>'.
							    'Подробности использования можно найти в руководстве. Оно находится в самой нижней панели сайта.<br/><br/>'.
								'* = обязательно';
	const TranslationNotes    = 'Если вы считаете нужным оставить комментарий или объяснение, используйте этот раздел.<br/><br/>'.
	                            'Здесь можно использовать специальный синтаксис, чтобы сделать текст более понятным.<br/><br/>'.
	                            '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                            '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                            '{nt}1{/nt}　→　Если указать строку такого вида и в словах, и в заметках, то между ними будут рабочие ссылки.<br/><br/>'.
							    'Подробности использования можно найти в руководстве. Оно находится в самой нижней панели сайта.<br/><br/>';
	const Controls            = 'Нажмите «Отменить», чтобы вернуться назад.<br/><br/>Нажмите «Отправить», чтобы сохранить изменения.';
}

namespace Localization\AboutPage
{
	const Heading = 'О сайте';
	
	const HeadingWhat = 'Что это за сайт?';
	const What = 'Этот сайт — база данных для песен, которые можно услышать в играх типа жанра «визуальная новелла». '.
	             'Во многих представителях жанра есть «опенинги», «эндинги» и «insert songs», как в фильмах, сериалах и мультсериалах. '.
				 'Наверное, читатель согласится, что песни в играх в целом — это далеко не самое частое явление. '.
				 'Именно поэтому саундтреки из визуальных новелл выделяются на фоне саундтреков из других игр. '.
				 'Но так как основным производителем игр данного жанра является Япония, то большинство песен и написано на японском, что несколько усложняет поиск текстов и переводов для обывателя. '.
				 'А если отметить, что к жанру не относится понятие «мейнстрим», то ситуация выглядит ещё печальнее.';
	
	const HeadingWhy = 'Зачем же нужен сайт?';
	const Why = 'Если вы любите музыку, то наверняка любите подпевать в такт, а также просто интересоваться, о чём же песни на других языках рассказывают. '.
	            'А это значит, что скорее всего вы пробовали искать слова и переводы и слышали о таких сайтах, как genius.com. '.
				'Тут возникает одно «но»: этот сайт сделан для аудитории западного полушария, а потому с контента с восточного не так уж и много. '.
				'И если подумать, то есть ещё одна проблема: такие сайты фокусируются на группах и индивидуальных исполнителях, и не имеют связи с другим медиа-контентом, как игры или мультсериалы. '.
				'Для заполнения этой маленькой ниши и существует этот сайт: песни связаны с исполнителями, играми и персонажами (скорее всего вам знакомы слово «сейю» и фраза «character voice»).';
	
	const HeadingHow = 'Откуда взять слова песни?';
	const How = 'Саундтреки для визуальных новелл часто сопровождаются небольшой книжкой со словами к музыке, комментариями авторов и другим контентом. '.
	            'Такое встречается как в физических, так и в цифровых копиях. '.
	            'Поэтому, чтобы написать слова, чаще всего проще купить саундтрек у официального продавца или найти фотографии, скриншоты и сканы таких книжек в интернете.';
	
	const HeadingWho = 'Кто может публиковать тексты песен?';
	const Who = 'Этот сайт — своеобразная вики, где каждый зарегистрировавшийся участник может внести вклад. '.
	            'И вы тоже можете поделиться чем-то с другими любителями музыки и саундтреков из визуальных новелл.';
}

namespace Localization\PolicyPage
{
	const Heading = 'Политика сайта';
	
	const Introduction = 'Данный сайт является бесплатной базой данных. '.
	                     'Это некоммерческий проект, созданный для того, чтобы делиться текстами и переводами редкой музыки со всем миром. '.
	                     'К проекту не имеют отношения к слова «реклама», «продакт-плейсмент» и «прибыль». '.
	                     'Здесь нельзя ничего купить, продать и даже пожертвовать. Можно только делиться знанием.';
	
	const Warning = 'Используя сайт, вы подтверждаете своё согласие со всеми условиями ниже.';
	
	const HeadingContent  = 'Политика использования контента';
	const ContentPolicy1  = '1. Весь вклад пользователя является доступным для любого человека в интернете. Пользователь не может предъявлять права на любой контент, созданный им или другим участником.';
	const ContentPolicy2  = '2. Имеющиеся на сайте изображения собраны из разных источников и могут быть объектом прав их соответствующих правообладателей.';
	const ContentPolicy3  = '3. Тексты песен могут быть написаны с помощью покупных изданий музыки или записаны на слух.'.
	                        'Поэтому контент может походить на содержание сайтов по похожей теме. В любом из случаев, тексты принадлежат только правообладателям песен.';
	const ContentPolicy4  = '4. Переводы песен принадлежат веб-сайту. Никто, включая веб-сайт и его владельцев, не вправе использовать переводы для получения любого вида выгоды или преимуществ.';
	const ContentPolicy5  = '5. Любая сущность на сайте или её часть может быть убрана по требованию её правообладателя.';
	const ContentPolicy6  = '6. Сайт и его администрация не несут ответственности за возможный урон и другие негативные последствия, вызванные работой сайта или его содержанием. '.
	                        'Ошибки в работе сайта и злонамеренные действия третьей стороны также относятся к этому правилу.';
	const ContentPolicy7  = '7. Пользователь не имеет права извлекать прибыль, используя содержание сайта или его часть.';
	const ContentPolicy8  = '8. Пользователь не имеет права использовать контент сайта для целей машинного обучения.';
	const ContentPolicy9  = '9. Пользователь не имеет права копировать, изменять, брать за основу или использовать любую часть контента сайта без ссылки на страницу и автора работы.';
	const ContentPolicy10 = '10. Политика может быть изменена без уведомления пользователей.';
	
	const HeadingPrivacy = 'Политика конфиденциальности';
	const PrivacyPolicy1 = '1. Пользователь использует сайт на свой риск. Сервис предоставляется на базисе «как есть». Пользователю не даются никакие гарантии на что бы то ни было.';
	const PrivacyPolicy2 = '2. Весь интернет-трафик о пользователе (например, используемый браузер и IP-адрес) может храниться неопределённый промежуток времени.';
	const PrivacyPolicy3 = '3. Информация о пользователе собирается в автоматическом режиме и не предоставляется третьим лицам, за исключением случаев официального обращения органов власти или стороны, предоставляющей услуги хостинга.';
	const PrivacyPolicy4 = '4. Сайт использует куки браузера. На данный момент хранится только уникальный токен сессии.';
	const PrivacyPolicy5 = '5. Сайт предоставляет возможность немедленного удаления аккаунта пользователя. В этом случае весь вклад пользователя будет сохранён и будет относится к «Удалённому пользователю».';
	const PrivacyPolicy6 = '6. Политика может быть изменена без уведомления пользователей.';
	
	const HeadingRightholder = 'Для правообладателя';
	const RightholderPolicy1 = '1. Если вас не устраивает какая-либо часть контента, принадлежащего вам, просим обратиться на почту support@vn-song-lyrics-db.ru.';
	const RightholderPolicy2 = '2. Администрация сайта всегда готова найти решение для ваших претензий.';
	
	const LastUpdated        = 'Последнее изменение: 17 февраля 2026.';
	const Timezone           = 'Часовой пояс веб-сайта: UTC+3.';
}

namespace Localization\RulesPage
{
	const Heading = 'Правила сайта';
	
	const HeadingGeneral = 'Правила поведения';
	
	const GeneralRule1  = '1. Ожидается, что пользователю исполнилось 18 лет. Сайт не просит подтверждения возраста, но страна проживания может накладывать свои ограничения на пользователя. Пользователь использует сайт на свой страх и риск.';
	const GeneralRule2  = '2. Имя пользователя не должно носить оскорбительный, неприличный или политический характер.';
	const GeneralRule3  = '3. Пользователю не разрешается публиковать контент, не связанный с тематикой сайта.';
	const GeneralRule4  = '4. В базу данных вносятся только официальные издания. Так называемые «gamerip» не разрешены.';
	const GeneralRule5  = '5. Визуальные новеллы могут иметь много альбомов, относящихся прямо или косвенно к ним. Все такие издания разрешены.';
	const GeneralRule6  = '6. Администрация сайта оставляет за собой право удалять любой контент, имеющий сомнительный или противоречивый характер.';
	const GeneralRule7a = '7. Администрация сайта оставляет за собой право редактировать и удалять любой контент, не следующий правилам ';
	const GeneralRule7b = 'Руководства';
	const GeneralRule7c = '.';
	const GeneralRule8  = '8. Администрация сайта оставляет за собой право ограничивать доступ к ресурсу полностью или только часть функционала любого пользователя без объяснения причин и уведомлений.';
	
	const HeadingAccess = 'Правила доступа к страницам';
	
	const AccessRule1 = '1. Любой пользователь, зарегистрированный или нет, может оставить анонимную жалобу на любой контент.';
	const AccessRule2 = '2. Любой пользователь, зарегистрированный или нет, может оставить публичный отзыв на сайте.';
	const AccessRule3 = '3. Зарегистрированный пользователь может добавлять сущность любого типа.';
	const AccessRule4 = '4. Зарегистрированный пользователь может редактировать сущность типа «Игра», «Альбом», «Исполнитель», and «Персонаж», если их статус — «Ожидает подтверждения администрацией».';
	const AccessRule5 = '5. Зарегистрированный пользователь может редактировать сущность типа «Текст песни», если её статус — «Ожидает подтверждения администрацией» и если данная сущность не имеет «Переводов».';
	const AccessRule6 = '6. Зарегистрированный пользователь может редактировать сущность типа «Перевод» вне зависимости от статуса. Любые изменения устанавливают статус «Ожидает подтверждения администрацией».';
	const AccessRule7 = '7. Зарегистрированный пользователь, ограниченный в правах из-за нарушения правил, может не иметь доступа к страницам, связанным с изменением любых данных. У пользователя есть право обжаловать нарушения по почте.';
	const AccessRule8 = '8. Только администрация сайта устанавливает статусы для контента.';
}

namespace Localization\WritingGuidePage
{
	const Heading = 'Руководство для писателя и переводчика';
	
	const Contents         = 'Содержание:';
	const LyricsLink       = 'Как написать слова к песне?';
	const TranslationLink  = 'Как написать перевод текста песни?';
	const RomanizationLink = 'Как написать романизацию текста, имени и названия?';
	const FormattingLink   = 'Как применить форматирование текста?';
	const CaptchaLink      = 'Как написать код captcha';
	
	const HeadingLyrics       = 'Как написать слова к песне?';
	const LyricsIntroduction1 = 'Написать слова к песне можно следующими способами:';
	const LyricsIntroduction2 = '- Если у вас есть исходник в любой форме (цифровая версия текста, фото, скан и т.д.), то их нужно просто написать';
	const LyricsIntroduction3 = '- Возможно, на каком-то другом сайте есть текст песни. Вы можете скопировать после того, как узнаете, нужно ли оставлять ссылку на сайт, с которого взят контент.';
	const LyricsIntroduction4 = '- Если текста нет ни в какой форме, то можно положиться на свои знания и умения и написать текст на слух.';
	const LyricsHeadsUp       = 'Обратите внимание на правила публикации:';
	const LyricsRule1         = '1. Слова к песне пишутся полностью. Нельзя делать какие-либо ссылки на другие части текста, например: ※repeat, [повтор куплета 1].';
	const LyricsRule2         = '2. Пишутся только слова песни. Ремарки в стиле [Куплет 1] и [Поёт: имя любого исполнителя] не разрешаются.';
	const LyricsRule3         = '3. Текст песни нельзя цензурировать, даже если там есть неприличные слова.';
	const LyricsRule4         = '4. Текст песни нельзя исправлять. Например, в тексте японской песни может быть грамматически некорректная фраза на английском. Слова пишутся именно в соответствии с исполнением и источником.';
	const LyricsRule5         = '5. Текст песни нужно исправлять, если исполнение имеет отличный текст от источника.';
	const LyricsRule6         = '6. Текст песни после написания нужно проверить на слух.';
	const LyricsRule7         = '7. Если в тексте песни встречаются слова, фразы или взгляды, которые могут противоречить какому-либо законодательству, обратитесь перед публикацией на почту support@vn-song-lyrics-db.ru.';
	const LyricsRule8         = '8. Узнайте, как можно форматировать текст.';
	const LyricsRule9         = '9. Используйте раздел «Заметки», если есть желание добавить какой-либо комментарий или пояснение.';
	
	const HeadingTranslation = 'Как написать перевод текста песни?';
	
	const TranslationIntroduction = 'Ошибка начинающего переводчика — сопоставлять слова и переводы из словаря. Так делать не надо.';
	
	const TranslationRemember1 = 'Хороший переводчик держит в голове:';
	const TranslationRemember2 = '- разницу в культурных аспектах;';
	const TranslationRemember3 = '- исходное звучание текста;';
	const TranslationRemember4 = '- соответственно красивые фразы на языке перевода;';
	const TranslationRemember5 = '- все известные ему тропы;';
	const TranslationRemember6 = '- стили речи, как на языке песни, так и на языке перевода;';
	const TranslationRemember7 = '- и, разумеется, значения фраз и текста.';
	const TranslationRemember8 = 'Может быть, даже что-то ещё? Если да, то читатель — умница.';
	
	const TranslationHeadsUp = 'Правила и рекомендации:';
	const TranslationRule1   = '1. Никакого ИИ и автоматических переводчиков. Можно использовать только естественный интеллект.';
	const TranslationRule2   = '2. Используйте словари, объясняющие значения слов на том же самом языке. Объяснение термина лучше, чем прямой ответ данный автором какого-либо переводческого словаря. '.
	                           'Не говоря уж о том, что тот переводчик может ошибаться, а его ответ — устареть.';
	const TranslationRule3   = '3. Используйте сайты, посвящённые изучению языков: например, stackexchange.com, hinative.com и другие. Объяснение современного обывателя может быть крайне полезно.';
	const TranslationRule4   = '4. Ищите примеры использования, если сомневаетесь в какой-либо фразе: тексты и посты в интернете, песни и стихи, словари и базы данных, шортсы и видео на видеохостингах. Используйте всё.';
	const TranslationRule5   = '5. Сделайте поисковик своим союзником: узнайте, как использовать его «с умом». Например, использовать прямые кавычки позволяют искать точную фразу: "I love you" вместо I love you.';
	const TranslationRule6   = '6. Используйте типографские символы: «кавычки-ёлочки», “кавычки-лапки”, многоточие… и всё остальное. А ещё обязательно писать букву «Ё». Красота требует жертв.';
	
	const TranslationLanguages = 'Маленькое отступление. Может случиться так, что желаемого языка нет на сайте. Напишите нам на почту или оставьте комментарий в отзывах.';
	
	const HeadingRomanization = 'Как написать романизацию текста, имени и названия?';
	
	const RomanizationIntroduction = 'Этот раздел посвящён именно романизации японского языка, как языка, на котором исполняется большинство песен. '.
	                                 'Выбор правил для романизации других языков оставляется на усмотрение пользователя.';
	
	const RomanizationJapanese1 = 'Итак, японский язык. Вероятнее всего, читатель знаком с правилами Хэпбёрна или что-то хотя бы слышал о них. Здесь мы используем другие правила, но в чём-то похожие. '.
	                              'Правила будут объясняться больше в схемах и примерах, чем в словах. Наверное, так будет намного проще.';
	const RomanizationJapanese2 = 'Перед тем, как начать читать, маленькое напоминание: при регистрации вы согласились соблюдать эти правила. Нравятся они вам или нет.';
	
	const HeadingAllowedSymbols = 'Разрешённые символы';
	const AllowedSymbols        = 'Романизация использует только те символы, которые присутствуют на клавиатуре. Это правило №1.';
	
	const HeadingNameOrder = 'Порядок в именах';
	const NameOrder        = 'В Японии сначала указывается фамилия, а потом имя. Такой порядок должен быть сохранён.';
	
	const HeadingCapitalization = 'Заглавные буквы в названиях';
	const Capitalization        = 'В названиях все слова, кроме частиц, пишутся с большой буквы.';
	
	const KanaConversionRules = 'Правила транслитерации каны';
	const KanaConversionNote  = 'Важно:';
	
	const KanaLanguageRules = 'Транслитерация катаканы, хираганы, кандзи и других языков';
	const KanaLanguageRule1 = '1. Слова, записанные на японском, пишутся в строчном регистре, согласно таблице сверху.';
	const KanaLanguageRule2 = '2. Заимствования никогда не заменяются на слова, от которых они были образованы.';
	const KanaLanguageRule3 = '3. Слова, записанные на других языках, пишутся в заглавном регистре.';
	
	const ParticlesRules      = 'Слитно или раздельно';
	const Together            = 'слитно';
	const Apart               = 'раздельно';
	const DifferenceInMeaning = 'Разница в значении';
	const SpecialReadings     = 'Особые чтения';
	const DivisionBySyllables = 'Запись по слогам';
	const OldWritingStyle     = 'Подражание старому стилю';
	
	const HeadingFormatting      = 'Как применить форматирование текста?';
	const FormattingIntroduction = 'Текстовый редактор поддерживает специальный синтаксис для:';
	const FormattingEntity1      = '- фуриганы;';
	const FormattingEntity2      = '- цветного текста;';
	const FormattingEntity3      = '- создания ссылок на сноски.';
	
	const FormattingFurigana1 = '1. Фуригана создаётся с помощью записи:';
	const FormattingFurigana2 = ', где * — любое количество любых символов.';
	const FormattingFurigana3 = 'Пример:';
	const FormattingFurigana4 = 'Применение: нестандартные, особые или неоднозначные чтения, а также редкие слова.';
	
	const FormattingColor1 = '2. Цветной текст создаётся с помощью записи:';
	const FormattingColor2 = ', где XXXXXX — шестнадцатеричное число, кодирующее цвет в палитре RGB и где * — любое количество любых символов.';
	const FormattingColor3 = 'Пример:';
	const FormattingColor4 = 'Применение: несколько исполнителей с разными строками.';
	
	const FormattingNotes1 = '3. Ссылки на сноски и текст создаются с помощью записи:';
	const FormattingNotes2 = ', где N — любое натуральное число. Повторно обращаем внимание: чтобы ссылка работала, нужно поместить её как в слова, так и в заметки.';
	const FormattingNotes3 = 'Пример, как это работает: ';
	const FormattingNotes4 = 'Применение: тексты и заметки.';
	
	const FormattingExample1 = 'Если вам интересны данные возможности, посмотрите ';
	const FormattingExample2 = 'пример текста';
	const FormattingExample3 = '.';
	
	const FormattingWarnings = 'Данные функции возможно использовать только в следующих секциях';
	const FormattingWarning1 = '- «Слова песни»;';
	const FormattingWarning2 = '- «Заметки»;';
	const FormattingWarning3 = '- «Перевод слов»;';
	const FormattingWarning4 = '- «Заметки переводчика».';
	const FormattingWarning5 = 'Мы не настаиваем на использовании этих функций. Используйте их по своему желанию.';
	
	const HeadingCaptcha = 'Как написать код captcha';
}

namespace Localization\LyricsExamplePage
{
	const Heading    = 'Пример текста с форматированием';
	const Formatting = 'Форматирование';
	const Japanese   = 'Японский';
	const Markup     = 'Разметка в редакторе';
	const Result     = 'Результат';
	const Notes      = 'Заметки';
}

namespace Localization\ErrorPage\BadRequest400
{
	const Reason = 'Введено некорректное или запрещённое значение.';
	const Hint   = 'Вернитесь и проверьте ввод ещё раз.';
}

namespace Localization\ErrorPage\Unauthorized401
{
	const Reason = 'Доступ к этой странице могут иметь зарегистрированные пользователи.';
	const Hint   = 'Убедитесь, что вы вошли в свой аккаунт.';
}

namespace Localization\ErrorPage\PaymentRequired402
{
	const Reason = 'Только те, кто платит за сервер, имеют сюда доступ.';
	const Hint   = '~♪~ Хорошего дня ~♪~';
}

namespace Localization\ErrorPage\Forbidden403
{
	const Reason = 'Вам не разрешён доступ на эту страницу.';
	const Hint   = 'Способа получить доступ не существует.';
}

namespace Localization\ErrorPage\NotFound404
{
	const Reason = 'Запрошенная страница не найдена.';
	const Hint   = 'Может быть, её переименовали?';
}

namespace Localization\ErrorPage\MethodNotAllowed405
{
	const Reason = 'Сервер понял запрос, но выбранный метод не разрешён.';
	const Hint   = 'Пожалуйста, используйте сайт так, как это задумывалось.';
}

namespace Localization\ErrorPage\NotAcceptable406
{
	const Reason = 'Выбранный язык не поддерживается сайтом.';
	const Hint   = 'Выберите один из языков вверху страницы.';
}

namespace Localization\ErrorPage\Conflict409
{
	const Reason = 'Добавляемые данные уже существуют в базе данных.';
	const Hint   = 'Просим проверять наличие данных до ввода.';
}

namespace Localization\ErrorPage\ContentTooLarge413
{
	const Reason = 'Загружаемый файл имел вес больше допустимого.';
	const Hint   = 'Пожалуйста, вернитесь и выберите другой файл.';
}

namespace Localization\ErrorPage\UnsupportedMediaType415
{
	const Reason = 'Загружаемый файл не принадлежал к разрешённому формату.';
	const Hint   = 'Пожалуйста, вернитесь и выберите другой файл.';
}

namespace Localization\ErrorPage\UnprocessableEntity422
{
	const Reason = 'Вводимые вами данные не смогли пройти проверку на сервере.';
	const Hint   = 'Пожалуйста, напишите подробности нам на почту support@vn-song-lyrics-db.ru.';
}

namespace Localization\ErrorPage\UnavailableForLegalReasons451
{
	const Reason = 'Доступ к этой странице ограничен по официальному требованию.';
	const Hint   = 'На данный момент способа получить доступ нет.';
}

namespace Localization\ErrorPage\InternalServerError500
{
	const Reason = 'В ходе обработки вашего запроса обнаружилась критическая ошибка.';
	const Hint   = 'Пожалуйста, напишите подробности нам на почту support@vn-song-lyrics-db.ru.';
}

namespace Localization\ErrorPage\NotImplemented501
{
	const Reason = 'Запрошенный метод не является известным.';
	const Hint   = 'Пожалуйста, используйте сайт так, как это задумывалось.';
}

namespace Localization\ErrorPage\BadGateway502
{
	const Reason = 'Сервер получил ошибку от другого требуемого сервера.';
	const Hint   = 'Попробуйте ещё раз немного позже.';
}

namespace Localization\ErrorPage\ServiceUnavailable503
{
	const Reason = 'Сайт недоступен, так как ведутся технические работы.';
	const Hint   = 'Пожалуйста, приходите позже.';
}
