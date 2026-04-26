<?php

namespace Localization\Functions
{
	function localizeLanguageName(array $entity): string
	{
		return $entity['language_en_name'];
	}

	function localizeLanguageKey(): string
	{
		return 'language_en_name';
	}

	function localizeTranslationNumber(int $number): string
	{
		return ' #'.$number;
	}
	
	use InputError;
	
	function localizeInputError(InputError $error): string|null
	{
		switch ($error)
		{
			case InputError::None:
				return null;
			
			case InputError::CaptchaInvalid:
				return 'The code was incorrect.';
			
			case InputError::EmailNotFound:
				return 'The email was not found.';
			
			case InputError::EmailInvalid:
				return 'The email was not written in the correct form.';
			
			case InputError::EmailTaken:
				return 'The email can not be used.';
			
			case InputError::UsernameForbiddenSymbols:
				return 'The username had forbidden symbols.';
			
			case InputError::UsernameLengthIncorrect:
				return 'The username had incorrect length.';
			
			case InputError::UsernameTaken:
				return 'The username is taken.';
			
			case InputError::IncorrectPassword:
				return 'The password was incorrect.';
			
			case InputError::PasswordForbiddenSymbols:
				return 'The password had forbidden symbols.';
			
			case InputError::PasswordLengthIncorrect:
				return 'The password had incorrect length.';
			
			default:
				throw new Exception(__FUNCTION__.': value '.$error->name.' was not found');
		}
	}
}

namespace Localization\HomePage
{
	const Heading          = 'Visual Novel Song Lyrics Database';
	const DescriptionOne   = 'vn-song-lyrics-db.ru strives to be a database for songs that make appearance in visual novels.';
	const DescriptionTwo   = 'These songs are not mainstream, so it is a challenge to find lyrics for them, not to mention translations.';
	const DescriptionThree = 'This website reminds of a wiki where every registered user may freely contribute to the database.';
	const LastAlbums       = 'Recently Added Albums';
	const LastLyrics       = 'Recently Added Lyrics';
	const LastTranslations = 'Recently Added Translations';
}

namespace Localization\Header
{
	const HomePage        = 'vn-song-lyrics-db';
	
	const GameList        = 'Games';
	const AlbumList       = 'Albums';
	const ArtistList      = 'Artists';
	const CharacterList   = 'Characters';
	const SongList        = 'Songs';
	const TranslationList = 'Translations';
	const Feedback        = 'Feedback';
	
	const LogIn           = 'Log In';
	const SignUp          = 'Sign Up';
	const LogOut          = 'Log Out';
}

namespace Localization\Footer
{
	const About         = 'About Website';
	const Policy        = 'Website Policy';
	const BehaviorRules = 'Website Rules';
	const WritingGuide  = 'Writing Guide';
}

namespace Localization\LogInPage
{
	const Heading                = 'Log In';
	
	const HintPassword           = 'If you lost it, contact us at support@vn-song-lyrics-db.ru';
	
	const Email                  = 'Email';
	const Password               = 'Password';
	const Submit                 = 'Log In';
}

namespace Localization\SignUpPage
{
	const Heading  = 'Sign Up';
	
	const Username = 'Username';
	const Password = 'Password';
	const Email    = 'Email';
	const Submit   = 'Sign Up';
	
	const HintUsername = 'Allowed Characters: A-Z, a-z, 0-9. Length: 4-32.';
	const HintEmail    = 'Used only to verify and identify you.';
	const HintPassword = 'Allowed Characters: A-Z, a-z, 0-9. Length: 4-32.';
	
	const Confirmation = 'By clicking “Sign Up”, you confirm that you agree with:';
	const Policy       = 'Website Policy';
	const Rules        = 'Website Rules';
	const WritingGuide = 'Writing Guide';
	const Warning      = 'Make sure you spent at least one minute for each link.';
	
	const AwaitingVerification = 'We have sent you a verification mail. Please, check your inbox and spam folder.';
}

namespace Localization\GameListPage
{
	const Heading = 'List of Games';
	const AddGame = 'Add New Game';
}

namespace Localization\AlbumListPage
{
	const Heading  = 'List of Albums';
	const AddAlbum = 'Add New Album';
}

namespace Localization\ArtistListPage
{
	const Heading   = 'List of Artists';
	const AddArtist = 'Add New Artist';
}

namespace Localization\CharacterListPage
{
	const Heading      = 'List of Characters';
	const AddCharacter = 'Add New Character';
}

namespace Localization\SongListPage
{
	const Heading     = 'List of Songs';
	const SongName    = 'Song Title';
}

namespace Localization\TranslationListPage
{
	const Heading = 'List of Translations';
}

namespace Localization\FeedbackPage
{
	const Heading             = 'Feedback';
	
	const Introduction        = 'Here you may say a couple of words about the website.';
	const MessagePublic       = 'All messages written here are public.';
	const AboutAnswer         = 'The staff read everything and may give a reply here.';
	const SymbolLimit         = 'The message is limited with 500 symbols.';
	
	const TextareaPlaceholder = 'Text of your message';
	const AnonymousAuthor     = 'Anonymous';
	const ReplyFromStaff      = 'Website Staff ';
	const Submit              = 'Submit';
	
	const Delete              = 'Delete Entry';
	const SendReply           = 'Send/Replace Reply';
}

namespace Localization\GamePage
{
	const Details           = 'Details: ';
	const RelatedCharacters = 'Related Characters';
	const RelatedAlbums     = 'Related Albums';
}

namespace Localization\AlbumPage
{
	const Details      = 'Details: ';
	const SongCount    = 'Track Count: ';
	
	const SongList     = 'Track List';
	const NoSongsAdded = 'Tracks have not been added to the album yet.';
	const RelatedGames = 'Related Games';
	
	const DiscNumber   = 'Disc';
	const TrackNumber  = 'Track';
	const SongName     = 'Track Title';
	
	const AddSong      = 'Add Track';
	const EditSong     = 'Edit Track';
	const FillAlbum    = 'Fill Album with vgmdb.net';
}

namespace Localization\ArtistPage
{
	const Details        = 'Details: ';
	const AliasOf        = 'Alias of: ';
	const Aliases        = 'Aliases';
	const RelatedSongs   = 'Related Songs';
}

namespace Localization\CharacterPage
{
	const Details      = 'Details: ';
	const RelatedGames = 'Related Games';
	const RelatedSongs = 'Related Songs';
}

namespace Localization\SongPage
{
	// nothing here yet
}

namespace Localization\LyricsPage
{
	const LyricsHeadingStart          = 'Lyrics of “';
	const LyricsHeadingEnd            = '”';
	
	const TranslationHeadingStart     = 'Translation of “';
	const TranslationHeadingMiddle    = '” (';
	const TranslationHeadingEnd       = ')';
	
	const NoLyricsAdded               = 'Lyrics have not been added yet.';
	const AddLyrics                   = 'Add Lyrics';
	const EditLyrics                  = 'Edit Lyrics';
	const DeleteLyrics                = 'Delete Lyrics';
	const ReportLyrics                = 'Report Lyrics';
	
	const AddLyricsUnavailableNoVoice = 'This song is instrumental.';
	const AddLyricsUnavailableForCopy = 'But you can write lyrics for the original song.';
	
	const AddTranslation              = 'Add Translation';
	const EditTranslation             = 'Edit Translation';
	const DeleteTranslation           = 'Delete Translation';
	const ReportTranslation           = 'Report Translation';
	
	const LyricsOf                    = 'Lyrics of ';
	const LyricsNotes                 = 'Notes';
	const LyricsNoNotes               = 'No notes have been provided.';
	
	const TranslationOf               = 'Translation of ';
	const TranslationNotes            = 'Notes';
	const TranslationNoNotes          = 'No notes have been provided.';
	
	const OriginalSong                = 'Original Song: ';
	const Album                       = 'Album: ';
	const ShowLyricsOnly              = 'Song: ';
	const TranslationList             = 'Translations: ';
	const PerformerList               = 'Performers: ';
	
	const ListElementSeparator        = ', ';
	const CvOpeningBracket            = ' (CV. ';
	const CvClosingBracket            = ')';
}

namespace Localization\GameEditorPage
{
	const HeadingAdd         = 'Add Game';
	const HeadingEdit        = 'Edit Game: ';
	
	const OriginalName       = 'Original Title';
	const TransliteratedName = 'Romanized Title';
	const LocalizedName      = 'Localized Title';
	const VndbLink           = 'Link to Game at vndb.org';
	const UploadNewLogo      = 'Upload New Logo';
	const OldLogo            = 'Current Logo';
	const NewLogo            = 'New Logo';
	const Logo               = 'Logo';
	const RelatedAlbums      = 'Related Albums';
	const RelatedCharacters  = 'Related Characters';
}

namespace Localization\AlbumEditorPage
{
	const HeadingAdd         = 'Add Album';
	const HeadingEdit        = 'Edit Album: ';
	
	const OriginalName       = 'Original Title';
	const TransliteratedName = 'Romanized Title';
	const LocalizedName      = 'Localized Title';
	const VgmdbLink          = 'Link to Album at vgmdb.net';
	const UploadNewCover     = 'Upload New Cover';
	const OldCover           = 'Current Cover';
	const NewCover           = 'New Cover';
	const Cover              = 'Cover';
	const RelatedGames       = 'Related Games';
	const SongCount          = 'Song Count';
}

namespace Localization\ArtistEditorPage
{
	const HeadingAdd         = 'Add Artist';
	const HeadingEdit        = 'Edit Artist: ';
	
	const OriginalName       = 'Original Name';
	const TransliteratedName = 'Romanized Name';
	const LocalizedName      = 'Localized Name';
	const VgmdbLink          = 'Link to Artist at vgmdb.net';
	const UploadNewPhoto     = 'Upload New Photo';
	const OldPhoto           = 'Current Photo';
	const NewPhoto           = 'New Photo';
	const Photo              = 'Photo';
	const OriginalArtist     = 'Alias of';
}

namespace Localization\CharacterEditorPage
{
	const HeadingAdd         = 'Add Character';
	const HeadingEdit        = 'Edit Character: ';
	
	const OriginalName       = 'Original Name';
	const TransliteratedName = 'Romanized Name';
	const LocalizedName      = 'Localized Name';
	const VndbLink           = 'Link to Character at vndb.org';
	const UploadNewImage     = 'Upload New Image';
	const OldImage           = 'Current Image';
	const NewImage           = 'New Image';
	const Image              = 'Image';
	const RelatedGames       = 'Related Games';
}

namespace Localization\SongEditorPage
{
	const HeadingAdd         = ': Add Song';
	const HeadingEdit        = 'Edit Song: ';
	
	const NextDisc           = 'Disc: +1';
	const PreviousDisc       = 'Disc: −1';
	const DiscAndTrack       = 'Disc & Track';
	const DiscNumber         = 'Disc';
	const TrackNumber        = 'Track';
	const OriginalName       = 'Original Title';
	const TransliteratedName = 'Romanized Title';
	const LocalizedName      = 'Localized Title';
	const HasVocal           = 'Has Lyrics?';
	const HasVocalTrue       = 'Yes';
	const HasVocalFalse      = 'No';
	
	const SubmitLastSong     = 'Submit Last Song';
	const SubmitNonLastSong  = 'Submit and Start Next Song';
	const SubmitChanges      = 'Submit Changes';
}

namespace Localization\LyricsEditorPage
{
	const HeadingAdd         = 'Add Lyrics: ';
	const HeadingEdit        = 'Edit Lyrics: ';
	const ArtistAndCharacter = 'Artist & Character';
	const PerformsAs         = 'performs as';
	const OriginalSong       = 'Original Song';
	const Language           = 'Language';
	const Lyrics             = 'Lyrics';
	const Notes              = 'Notes';
}

namespace Localization\TranslationEditorPage
{
	const HeadingAdd        = 'Add Translation';
	const HeadingEdit       = 'Edit Translation';
	
	const SourceLanguage    = 'Original Language';
	const TargetLanguage    = 'Language of Translation';
	const SongName          = 'Original Title';
	const TranslationName   = 'Title of Translation';
	const SongLyrics        = 'Original Lyrics';
	const TranslationLyrics = 'Translation of Lyrics';
	const SongNotes         = 'Original Notes';
	const TranslationNotes  = 'Translator’s Notes';
}

namespace Localization\FillAlbumEditorPage
{
	const Heading            = 'Fill Album: ';
	
	const DiscNumber         = 'Disc';
	const TrackNumber        = 'Track';
	const OriginalName       = 'Original Title';
	const TransliteratedName = 'Romanized Title';
	const LocalizedName      = 'Localized Title';
	
	const HasVocal           = 'Has Lyrics?';
	const HasVocalTrue       = 'Yes';
	const HasVocalFalse      = 'No';
}

namespace Localization\DeleteEntityPage
{
	const DeleteGame        = 'Delete Game: ';
	const DeleteAlbum       = 'Delete Album: ';
	const DeleteArtist      = 'Delete Artist: ';
	const DeleteCharacter   = 'Delete Character: ';
	const DeleteSong        = 'Delete Song: ';
	const DeleteLyrics      = 'Delete Lyrics: ';
	const DeleteTranslation = 'Delete Translation: ';
	
	const Game              = 'Game';
	const Album             = 'Album';
	const Artist            = 'Artist';
	const Character         = 'Character';
	const Song              = 'Song';
	const Lyrics            = 'Lyrics';
	const Translation       = 'Translation';
	
	const Introduction      = 'Are you sure you want to delete this data?';
	const Warning           = 'This action can not be undone.';
}

namespace Localization\ReportPage
{
	const Heading            = 'Report: ';
	
	const Introduction       = 'You are going to submit an anonymous report to the website staff.';
	const AboutReportContent = 'It seems that you found incomplete, incorrect or inappropriate information. Please, share the details.';
	const NoActionWarning    = 'You are kindly asked to note that submitting a report does not imply an immediate action or any action at all.';
	const ReplyOpportunity   = 'If you expect a reply, please, send a mail to support@vn-song-lyrics-db.ru instead.';
	const SymbolLimitWarning = 'The message is limited with 250 symbols.';
	const Redirect           = 'After you submit the report, you will be redirected to the page of the reported subject.';
	
	const Game               = ' (Game)';
	const Album              = ' (Album)';
	const Artist             = ' (Artist)';
	const Character          = ' (Character)';
	const Song               = ' (Song)';
	const Lyrics             = ' (Lyrics)';
	const Translation        = ' (Translation)';
}

namespace Localization\UserPage
{
	const User                 = 'User: ';
	const Role                 = 'Role: ';
	
	const AccountControl       = 'Account Control Panel';
	const ChangeAccountData    = 'Change Account Data';
	const DeleteAccount        = 'Delete Account';
	
	const Contributions        = 'Contributions';
	const RelatedGames         = 'Games: ';
	const RelatedAlbums        = 'Albums: ';
	const RelatedArtists       = 'Artists: ';
	const RelatedCharacters    = 'Characters: ';
	const RelatedSongs         = 'Songs: ';
	const RelatedTranslations  = 'Translations: ';
}

namespace Localization\UserAccountDataPage
{
	const Edit            = ': Change';
	const AccountData     = 'Account Data';
	
	const Username        = 'Username';
	const Email           = 'Email';
	const NewPassword     = 'New Password';
	const NewPasswordNote = 'Only if needed';
	const OldPassword     = 'Current Password';
}

namespace Localization\UserAccountDeletePage
{
	const Delete       = ': Delete';
	const AccountData  = 'Warning';
	
	const Warning1     = 'You are going to delete your account.';
	const Warning2     = 'The website will forget your account data such as email.';
	const Warning3     = 'All your contributions will be kept and will be marked with “Deleted User”.';
	const Warning4     = 'This action is impossible to undo and comes into force immediately.';
	const Confirmation = 'Your password is required to confirm the action.';
	
	const Password     = 'Password';
}

namespace Localization\ImageAltText
{
	const NoGameLogo       = 'No Game Logo Uploaded';
	const NoAlbumCover     = 'No Album Cover Uploaded';
	const NoArtistPhoto    = 'No Artist Photo Uploaded';
	const NoCharacterImage = 'No Character Image Uploaded';
	
	const GameLogoOf       = 'Logo of Game ';
	const AlbumCoverOf     = 'Cover of Album ';
	const ArtistPhotoOf    = 'Photo of Artist ';
	const CharacterImageOf = 'Image of Character ';
}

namespace Localization\TimestampString
{
	const Added       = 'Added';
	const Updated     = 'Updated';
	const Reviewed    = 'Reviewed';
	const By          = ' by ';
	const DeletedUser = 'Deleted User';
	const Delimeter   = ' ';
}

namespace Localization\ModerationStatus
{
	const RelationStatus = 'Relation Status: ';
	const Status         = 'Status: ';
	const Unchecked      = 'Awaiting Staff Approval';
	const Checked        = 'Approved';
	const Hidden         = 'Hidden';
	const Unknown        = 'Unknown';
}

namespace Localization\Tooltip
{
	const UserVisitor         = 'Log in or sign up to contribute';
	const UserViolator        = 'You have been restricted from making any changes';
	const UserNotAuthor       = 'You are not the submitter of this information';
	const NotOriginalSong     = 'Go to the original song to see if you can edit the information there';
	const InfoHidden          = 'The information has been removed by the staff due to formal reasons';
	const InfoChecked         = 'The information has been approved by the staff';
	const SongHasTranslations = 'The song has translations';
	const OriginalLanguage    = 'It is the language of the song';
	const AlreadyTranslated   = 'You have already translated this song to this language';
}

namespace Localization\Controls
{
	const Report       = 'Report Issue on This Page';
	const Edit         = 'Edit Information';
	const Delete       = 'Delete Information';
	
	const SearchHeading     = 'Search over the database';
	const SearchPlaceholder = 'Start typing …';
	const SearchButton      = 'Search';
	const PageHeading       = 'Page';
	const LimitHeading      = 'Results per page';
	const NoLimit           = 'All';
	
	const FilterPage   = 'Filter results on this page …';
	const Textarea     = 'Start typing …';
	
	const ChooseFile   = 'Choose file (max. size: 512 KiB) …';
	const FileTooBig   = 'File is too big. Try another file …';
	
	const Cancel       = 'Cancel';
	const Confirmation = 'I confirm';
	const Submit       = 'Submit';
}

namespace Localization\TooltipWindow
{
	const DefaultHeading = 'Hint';
	const DefaultContent = 'Here you can find useful information about elements on the page.'.
							'</br></br>Hover over an item to find out details.';
}

namespace Localization\GameEditorPage\TooltipHeading
{
	const OriginalName       = 'Original Title';
	const TransliteratedName = 'Romanized Title';
	const LocalizedName      = 'Localized Title';
	const OldLogo            = 'Current Logo';
	const NewLogo            = 'New Logo';
	const Logo               = 'Logo';
	const VndbLink           = 'Link to vndb.org';
	const RelatedAlbums      = 'Related Albums';
	const RelatedCharacters  = 'Related Characters';
	const Controls           = 'Controls';
}

namespace Localization\GameEditorPage\TooltipContent
{
	const OriginalName       = 'The original name of the game.<br/><br/>'.
	                           'Please, check if the game is already present in the database before adding a new entry.<br/><br/>'.
							   '* = mandatory';
	const TransliteratedName = 'The original name of the game written in romaji.<br/><br/>'.
	                           'Please, check the writing guide on how to romanize names. You can find it in the footer.<br/><br/>'.
							   'Remember that the romanized name is not allowed have symbols other than printable ASCII.<br/><br/>'.
							   '* = mandatory';
	const LocalizedName      = 'The name used in international stores or vica versa: localized exclusively for its own market.<br/><br/>'.
	                           'If the game was never released for international community, leave this field empty.';
	const OldLogo            = 'The current logo of the game.';
	const NewLogo            = 'If you want to change the logo, upload a new image.<br/><br/>'.
	                           'Maximum size of the file: 512 kilobytes.<br/><br/>'.
							   'We advise you to use an image with the same width and height.';
	const Logo               = 'The logo of the game.<br/><br/>'.
	                           'Maximum size of the file: 512 kilobytes.<br/><br/>'.
							   'We advise you to use an image with the same width and height.';
	const VndbLink           = 'The link to the game in the database vndb.org.<br/><br/>'.
	                           'vndb.org focuses on visual novels and, perhaps, is the largest visual novel database in the internet.<br/><br/>'.
							   'Detailed information on the game is not the purpose of this website.<br/><br/>'.
							   'Please, find the game there and paste the link in this field.';
	const RelatedAlbums      = 'If some album has songs related to the game, then select it here.<br/><br/>'.
	                           'Use the plus button to add a new relation.<br/><br/>'.
	                           'Use the minus button to remove the relation.<br/><br/>'.
							   'If there are no albums related to it yet, then select nothing. You can add albums and relations later.<br/><br/>'.
	                           'If the field is not allowed to edit, it means that the staff has approved the relation.';
	const RelatedCharacters  = 'If some characters sing some song and is related to the game, then select it here.<br/><br/>'.
	                           'Use the plus button to add a new relation.<br/><br/>'.
	                           'Use the minus button to remove the relation.<br/><br/>'.
							   'If there are no characters related to it yet, then select nothing. You can add characters and relations later.<br/><br/>'.
	                           'If the field is not allowed to edit, it means that the staff has approved the relation.';
	const Controls           = 'Click the cancel button to go back.<br/><br/>Click the submit button to send the entry.';
}

namespace Localization\AlbumEditorPage\TooltipHeading
{
	const OriginalName       = 'Original Title';
	const TransliteratedName = 'Romanized Title';
	const LocalizedName      = 'Localized Title';
	const OldCover           = 'Current Cover';
	const NewCover           = 'New Cover';
	const Cover              = 'Cover';
	const VgmdbLink          = 'Link to vgmdb.net';
	const RelatedGames       = 'Related Games';
	const SongCount          = 'Track Count';
	const Controls           = 'Controls';
}

namespace Localization\AlbumEditorPage\TooltipContent
{
	const OriginalName       = 'The original name of the album.<br/><br/>'.
	                           'Please, check if the album is already present in the database before adding a new entry.<br/><br/>'.
							   '* = mandatory';
	const TransliteratedName = 'The original name of the album written in romaji.<br/><br/>'.
	                           'Please, check the writing guide on how to romanize names. You can find it in the footer.<br/><br/>'.
							   'Remember that the romanized name is not allowed have symbols other than printable ASCII.<br/><br/>'.
							   '* = mandatory';
	const LocalizedName      = 'The name used in international stores or vice versa: localized exclusively for its own market.<br/><br/>'.
	                           'If the album was never released for international community, leave this field empty.';
	const OldCover           = 'The current cover of the album.';
	const NewCover           = 'If you want to change the cover, upload a new image.<br/><br/>'.
	                           'Maximum size of the file: 512 kilobytes.<br/><br/>'.
							   'We advise you to use an image with the same width and height.';
	const Cover              = 'The cover of the album.<br/><br/>'.
	                           'Maximum size of the file: 512 kilobytes.<br/><br/>'.
							   'We advise you to use an image with the same width and height.';
	const VgmdbLink          = 'The link to the album in the database vgmdb.net.<br/><br/>'.
	                           'vgmdb.net mainly focuses on game soundtracks and has a big community supporting the website.<br/><br/>'.
							   'Detailed information on the album is not the purpose of this website.<br/><br/>'.
							   'Please, find the album there and paste the link in this field.';
							   //'Filling this field allows you to half-automatically populate the album with its track list after you submit it.';
	const RelatedGames       = 'If this album has songs related to some game that is already added, then select it here.<br/><br/>'.
	                           'Use the plus button to add a new relation.<br/><br/>'.
	                           'Use the minus button to remove the relation.<br/><br/>'.
							   'If there are no games related to it yet, then select nothing. You can add games and relations later.<br/><br/>'.
							   'If the field is not allowed to edit, it means that the staff has approved the relation.';
	const SongCount          = 'Enter the total track count on all disks of the album.<br/><br/>'.
	                           '* = mandatory';
	const Controls           = 'Click the cancel button to go back.<br/><br/>Click the submit button to send the entry.';
}

namespace Localization\ArtistEditorPage\TooltipHeading
{
	const OriginalName       = 'Original Name';
	const TransliteratedName = 'Romanized Name';
	const LocalizedName      = 'Localized Name';
	const OldPhoto           = 'Current Photo';
	const NewPhoto           = 'New Photo';
	const Photo              = 'Photo';
	const VgmdbLink          = 'Link to vgmdb.net';
	const OriginalArtist     = 'Alias of';
	const Controls           = 'Controls';
}

namespace Localization\ArtistEditorPage\TooltipContent
{
	const OriginalName       = 'The name of the artist in its original language.<br/><br/>'.
	                           'Please, check if the artist is already present in the database before adding a new entry.<br/><br/>'.
							   '* = mandatory';
	const TransliteratedName = 'The original name of the artist written in romaji.<br/><br/>'.
	                           'Please, check the writing guide on how to romanize names. You can find it in the footer.<br/><br/>'.
							   'Remember that the romanized name is not allowed have symbols other than printable ASCII.<br/><br/>'.
							   '* = mandatory';
	const LocalizedName      = 'The name used in international stores or vice versa: localized exclusively for its own market.<br/><br/>'.
	                           'If the artist was never advertized for international community, leave this field empty.';
	const OldPhoto           = 'The current photo of the artist.';
	const NewPhoto           = 'If you want to change the photo, upload a new image.<br/><br/>'.
	                           'Maximum size of the file: 512 kilobytes.<br/><br/>'.
							   'We advise you to use an image with the same width and height.';
	const Photo              = 'The photo of the artist.<br/><br/>'.
	                           'Maximum size of the file: 512 kilobytes.<br/><br/>'.
							   'We advise you to use an image with the same width and height.';
	const VgmdbLink          = 'The link to the artist in the database vgmdb.net.<br/><br/>'.
	                           'vgmdb.net mainly focuses on game soundtracks and has a big community supporting the website.<br/><br/>'.
							   'Detailed information on the artist is not the purpose of this website.<br/><br/>'.
							   'Please, find the artist there and paste the link in this field.';
	const OriginalArtist     = 'If the current artist is an alias of another one, fill this field, else leave it empty.';
	const Controls           = 'Click the cancel button to go back.<br/><br/>Click the submit button to send the entry.';
}

namespace Localization\CharacterEditorPage\TooltipHeading
{
	const OriginalName       = 'Original Name';
	const TransliteratedName = 'Romanized Name';
	const LocalizedName      = 'Localized Name';
	const OldImage           = 'Current Image';
	const NewImage           = 'New Image';
	const Image              = 'Image';
	const VndbLink           = 'Link to vndb.org';
	const RelatedGames       = 'Related Games';
	const Controls           = 'Controls';
}

namespace Localization\CharacterEditorPage\TooltipContent
{
	const OriginalName       = 'The name of the game character in its original language.<br/><br/>'.
	                           'Please, check if the character is already present in the database before adding a new entry.<br/><br/>'.
							   '* = mandatory';
	const TransliteratedName = 'The original name of the character written in romaji.<br/><br/>'.
	                           'Please, check the writing guide on how to romanize names. You can find it in the footer.<br/><br/>'.
							   'Remember that the romanized name is not allowed have symbols other than printable ASCII.<br/><br/>'.
							   '* = mandatory';
	const LocalizedName      = 'The name used in most of localized versions of the game or vice versa: localized exclusively for its own region.<br/><br/>'.
	                           'If the game was never released for international community, leave this field empty.';
	const OldImage           = 'The current image of the character.';
	const NewImage           = 'If you want to change the image, upload a new one.<br/><br/>'.
	                           'Maximum size of the file: 512 kilobytes.<br/><br/>'.
							   'We advise you to use an image with the same width and height.';
	const Image              = 'The image of the character.<br/><br/>'.
	                           'Maximum size of the file: 512 kilobytes.<br/><br/>'.
							   'We advise you to use an image with the same width and height.';
	const VndbLink           = 'The link to the game character in the database vndb.org.<br/><br/>'.
	                           'vndb.org focuses on visual novels and, perhaps, is the largest visual novel database in the internet.<br/><br/>'.
							   'Detailed information on the character is not the purpose of this website.<br/><br/>'.
							   'Please, find the character there and paste the link in this field.';
	const RelatedGames       = 'If this character has songs related to some game that is already added, then select it here.<br/><br/>'.
	                           'Use the plus button to add a new relation.<br/><br/>'.
	                           'Use the minus button to remove the relation.<br/><br/>'.
							   'If there are no games related to it yet, then select nothing. You can add games and relations later.<br/><br/>'.
							   'If the field is not allowed to edit, it means that the staff has approved the relation.';
	const Controls           = 'Click the cancel button to go back.<br/><br/>Click the submit button to send the entry.';
}

namespace Localization\SongEditorPage\TooltipHeading
{
	const DiscAndTrack       = 'Disc & Track';
	const OriginalName       = 'Original Title';
	const TransliteratedName = 'Romanized Title';
	const LocalizedName      = 'Localized Title';
	const HasVocal           = 'Has Lyrics';
	const Controls           = 'Controls';
}

namespace Localization\SongEditorPage\TooltipContent
{
	const DiscAndTrack       = 'The number of the disc and number of the track on it. This value is calculated automatically.<br/><br/>'.
	                           'Use the button to set a new value for the disc.<br/><br/>'.
	                           '* = mandatory';
	const OriginalName       = 'The title of the song in its original language.<br/><br/>'.
	                           '* = mandatory';
	const TransliteratedName = 'The original title of the song written in romaji.<br/><br/>'.
	                           'Please, check the writing guide on how to romanize names. You can find it in the footer.<br/><br/>'.
	                           'Remember that the romanized name is not allowed have symbols other than printable ASCII.<br/><br/>'.
							   '* = mandatory';
	const LocalizedName      = 'The title of the song used in most of localized versions of the album or vice versa: localized exclusively for its own region.<br/><br/>'.
	                           'If the album was never released for international community, leave this field empty.';
	const HasVocal           = 'If the song is instrumental, then select “No”. If the song has lyrics, then select “Yes”.<br/><br/>'.
	                           'Selecting “Yes” allows you to add its lyrics later. The song becomes clickable in the track list of the album.'.
							   '* = mandatory';
	const Controls           = 'Click the cancel button to go back.<br/><br/>Click the submit button to send the entry.';
}

namespace Localization\FillAlbumPage\TooltipHeading
{
	const TrackTable = 'Track List';
	const Controls   = 'Controls';
}

namespace Localization\FillAlbumPage\TooltipContent
{
	const TrackTable = 'The table was obtained from vgmdb.net.<br/></br>'.
	                   'The website does not know what titles are original, transliterated or localized. Please, select the appropriate titles in the first row.<br/><br/>'.
					   'If the table has transliterated names, then if they follow the style of the writing guide you can find in the footer.'.
					   'If the table does not have transliterated names, then convert it by yourself. We remind you of the writing guide you can find in the footer.<br/><br/>'.
					   'If the table has more columns than the required number (3), then do not select a header to them.<br/><br/>'.
					   'If the names of the songs were never localized, then select the header for an empty column and leave cells of this column empty.<br/><br/>'.
					   'Mark each song whether it has lyrics or it is instrumental.<br/><br/>'.
					   'Please, note that original and transliterated titles are mandatory.<br/></br>'.
					   'Remember that the romanized name is not allowed have symbols other than printable ASCII.';
	const Controls   = 'Click the cancel button to go back.<br/><br/>Click the submit button to send the entry.';
}

namespace Localization\LyricsEditorPage\TooltipHeading
{
	const ArtistAndCharacter = 'Artist & Character';
	const OriginalSong       = 'Original Song';
	const Language           = 'Language';
	const Lyrics             = 'Lyrics';
	const Notes              = 'Notes';
	const Controls           = 'Controls';
}

namespace Localization\LyricsEditorPage\TooltipContent
{
	const ArtistAndCharacter = 'Select singers of the song.<br></br>'.
	                           'If the song is performed by a game character (also known as Character Voice), then select the character too.<br/><br/>'.
	                           'Use the plus button to add one artist more.<br/><br/>'.
							   'Use the minus button to remove the artist<br/><br/>'.
							   '* = mandatory to select artists.';
	const OriginalSong       = 'If the song is a rearrangement, remix or a short version of another song, then use this option.<br/><br/>'.
	                           'Lyrics and translations of the original song will be automatically assigned to this song.';
	const Language           = 'Select the main language of the song.<br/><br/>'.
	                           'Some songs may have phrases of a different language in their lyrics. Select the prevailing language.<br/><br/>'.
							   '* = mandatory';
	const Lyrics             = 'Write the lyrics of the song.<br/><br/>'.
	                           'Here you can use special syntax to make the text more meaningful.<br/><br/>'.
	                           '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                           '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                           '{nt}1{/nt}　→　If used both in notes and lyrics, creates a clickable link between them.<br/><br/>'.
							   'More details on how to use these features are in the writing guide located in the footer of the page.<br/><br/>'.
							   '* = mandatory';
	const Notes              = 'Write any comments on the song if necessary.<br/><br/>'.
	                           'Here you can use special syntax to make the text more meaningful.<br/><br/>'.
	                           '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                           '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                           '{nt}1{/nt}　→　If used both in notes and lyrics, creates a clickable link between them.<br/><br/>'.
							   'More details on how to use these features are in the writing guide located in the footer of the page.';
	const Controls           = 'Click the cancel button to go back.<br/><br/>Click the submit button to send the entry.';
}

namespace Localization\TranslationEditorPage\TooltipHeading
{
	const TranslationLanguage = 'Language';
	const TranslationName     = 'Name';
	const TranslationLyrics   = 'Lyrics';
	const TranslationNotes    = 'Notes';
	const Controls            = 'Controls';
}

namespace Localization\TranslationEditorPage\TooltipContent
{
	const TranslationLanguage = 'Select the language you want to translate the song to.<br/><br/>'.
	                            'A user may not translate a song to the same language several times.<br/><br/>'.
	                            '* = mandatory';
	const TranslationName     = 'Translate the title of the song.<br/><br/>'.
	                            '* = mandatory';
	const TranslationLyrics   = 'Translate the lyrics of the song.<br/><br/>'.
	                            'Make sure you read recommendations in the writing guide.<br/><br/>'.
								'Here you can use special syntax to make lyrics more meaningful.<br/><br/>'.
	                            '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                            '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                            '{nt}1{/nt}　→　If used both in notes and lyrics, creates a clickable link between them.<br/><br/>'.
							    'More details on how to use these features are in the writing guide located in the footer of the page.<br/><br/>'.
								'* = mandatory';
	const TranslationNotes    = 'If you think that you should explain some phrases or make a comment in general, use this section.<br/><br/>'.
	                            'Here you can use special syntax to make lyrics more meaningful.<br/><br/>'.
	                            '{kj}漢字{fg}かんじ{/fg}　→　<ruby>漢字<rt>かんじ</rt></ruby><br/><br/>'.
	                            '{cl #FF0000}色づいた{/cl}言葉　→　<span style="color: #FF0000">色づいた</span>言葉<br/><br/>'.
	                            '{nt}1{/nt}　→　If used both in notes and lyrics, creates a clickable link between them.<br/><br/>'.
							    'More details on how to use these features are in the writing guide located in the footer of the page.<br/><br/>';
	const Controls            = 'Click the cancel button to go back.<br/><br/>Click the submit button to send the entry.';
}

namespace Localization\AboutPage
{
	const Heading = 'About Website';
	
	const HeadingWhat = 'What is this website?';
	const What = 'The website is a database for songs that you can hear in games of visual novel genre. '.
	             'These games usually have songs called “openings”, “inserts”, and “endings” like a cartoon or TV series. '.
				 'Probably, you would agree that it is not a common thing to do in games of other genres. '.
				 'That being said, you might be interested in official soundtracks of visual novels a little more because of these songs. '.
				 'However, most of the visual novels are made in Japan and it is rather difficult to find lyrics and translations for them. '.
				 'Not to mention, that the game genre is not mainstream at all, so the situation may be even more disappointing.';
	
	const HeadingWhy = 'Why is a website even needed?';
	const Why = 'If you like to listen to music, you might hum its lyrics and even may to be interested to know what the song is about. '.
	            'Of course, it means you probably tried to find its lyrics and, most probably, you know such websites as genius.com or lyricstranslate.com. '.
				'Being a websites made in countries of the Western Hemisphere, they do not really have songs came from the East. '.
				'And what’s more, here arises another problem: these websites are dedicated to individual artists that have no relation to games at all. '.
				'Here comes this service trying to fill the niche: songs here are related to games, artists and even game characters (usually accompanied by words “seiyuu” or “character voice”).';
	
	const HeadingHow = 'How are lyrics transcribed?';
	const How = 'Visual Novel soundtracks being sold officially usually have a dedicated book having comments of creators, lyrics, notes, and other stuff. '.
	            'It may come both in the paper form or in the digital copy. '.
	            'So, instead of transcribing by listening, it is easier to buy an edition of the soundtrack or simply look for such a book in the internet and write the lyrics using photos or screenshots.';
	
	const HeadingWho = 'Who publishes lyrics here?';
	const Who = 'This website is a free wiki where each and every one may contribute. '.
	            'You too may add something to share it with everyone who loves music made specifically for visual novels.';
}

namespace Localization\PolicyPage
{
	const Heading = 'Website Policy';
	
	const Introduction = 'The website strives to be a free database. '.
	                     'It is a non-commercial project intended to share lyrics and translations of songs from visual novels across the world. '.
	                     'The project rejects the idea of advertisement, product placement or any commercial profit. '.
	                     'There is no opportunity to donate, sell or buy. Here you can only share.';
	
	const Warning = 'By using the website, you agree with all policies mentioned below.';
	
	const HeadingContent  = 'Content Policy';
	const ContentPolicy1  = '1. All contributions are public and available for anybody on the Internet. The user agrees that they claim no rights on any contribution.';
	const ContentPolicy2  = '2. Images are gathered from different online sources and may be subject to the licenses of their respective rightholders.';
	const ContentPolicy3  = '3. Lyrics are either transcribed with the use of materials provided in official editions of soundtracks or by listening. '.
	                        'Therefore, they might look identical to the content on websites of the similar theme. In all cases, lyrics belong only to their respective rightholders.';
	const ContentPolicy4  = '4. Translations belong to the website. No one, including the website and its owners, is in right to use translations to make any kind of profit or benefit.';
	const ContentPolicy5  = '5. Any contribution may be removed on the legal note of the rightholder of the entity in question.';
	const ContentPolicy6  = '6. The website owner is not liable for any damage or another kind of negative consequence that may be caused by the website and its content. '.
	                        'It includes malfunctions and deliberate malicious actions by a third party.';
	const ContentPolicy7  = '7. The user agrees not to use any part of the website content for commercial purposes.';
	const ContentPolicy8  = '8. The user agrees not to use any part of the website content for AI machine learning purposes.';
	const ContentPolicy9  = '9. The user agrees not to copy, modify, derive or exploit any part of the website content with the purpose of distribution without a reference to the database and creator of the content in question.';
	const ContentPolicy10 = '10. The policy may be changed without notifying users.';
	
	const HeadingPrivacy = 'Privacy Policy';
	const PrivacyPolicy1 = '1. The user uses the service at their sole risk. The service is provided on basis “as is”. The website makes no warranties on anything.';
	const PrivacyPolicy2 = '2. All network traffic including information about the user (e.g. a browser or their IP address) may be kept for indefinite time.';
	const PrivacyPolicy3 = '3. The information about the user is collected automatically and is not shared with third parties, except for cases of legal notes from authorities or the hosting provider.';
	const PrivacyPolicy4 = '4. The website uses browser cookies. At the moment, the website uses the storage only for the unique session token.';
	const PrivacyPolicy5 = '5. The website provides an opportunity to delete the account immediately. In this case, all contributions remain and are marked with tag “Deleted User”.';
	const PrivacyPolicy6 = '6. The policy may be changed without notifying users.';
	
	const HeadingRightholder = 'For Rightholder';
	const RightholderPolicy1 = '1. If you are not comfortable with the fact that any data about you or your work is included in the database, please, contact us at support@vn-song-lyrics-db.ru.';
	const RightholderPolicy2 = '2. The website is ready to find a solution to your claims.';
	
	const LastUpdated        = 'Policies were last edited on 17 February 2026.';
	const Timezone           = 'All dates and times are UTC+3.';
}

namespace Localization\RulesPage
{
	const Heading = 'Website Rules';
	
	const HeadingGeneral = 'Behavior Rules';
	
	const GeneralRule1  = '1. The user is expected to be over 18 years of age. The website do not require your age, but your country may have limitations. Visit the website at your own risk.';
	const GeneralRule2  = '2. It is not allowed to have a username having obscene, abusive or political meaning.';
	const GeneralRule3  = '3. It is not allowed to post any materials that are not related to the website theme.';
	const GeneralRule4  = '4. Only official soundtracks are allowed. Gamerips are not allowed.';
	const GeneralRule5  = '5. Visual novels may have a lot of albums containing songs related to them. All of them are welcome here.';
	const GeneralRule6  = '6. The website staff reserves the right to remove any controversial content.';
	const GeneralRule7a = '7. The website staff reserves the right to edit or remove content that violates ';
	const GeneralRule7b = 'Writing Guide';
	const GeneralRule7c = '.';
	const GeneralRule8  = '8. The website staff reserves the right to block the access to the website for any user or restrict their functional without a notification and explanation.';
	
	const HeadingAccess = 'Access Rules';
	
	const AccessRule1 = '1. Any user, registered or not, may file an anonymous report on any contribution.';
	const AccessRule2 = '2. Any user, registered or not, may submit feedback.';
	const AccessRule3 = '3. A registered user may add a contribution of any type.';
	const AccessRule4 = '4. A registered user may edit or delete their contribution of type “Game”, “Album”, “Artist”, and “Character” if its status is “Awaiting Staff Approval”.';
	const AccessRule5 = '5. A registered user may edit or delete their contribution of type “Lyrics” if its status is “Awaiting Staff Approval” and if the “Lyrics” in question has no “Translations”.';
	const AccessRule6 = '6. A registered user may edit or delete their contribution of type “Translation” regardless the approval. Doing so changes the status to “Awaiting Staff Approval”.';
	const AccessRule7 = '7. A user known for violating rules may be restricted from doing any changes on the website on any period. A violator may appeal through contacting the staff by mail.';
	const AccessRule8 = '8. Only the website staff sets the status of the contribution.';
}

namespace Localization\WritingGuidePage
{
	const Heading = 'Writing Guide';
	
	const Contents         = 'Contents:';
	const LyricsLink       = 'How To Write Lyrics';
	const TranslationLink  = 'How To Translate Lyrics';
	const RomanizationLink = 'How To Romanize Lyrics And Names';
	const FormattingLink   = 'How To Apply Formatting';
	const CaptchaLink      = 'How To Solve Captcha';
	
	const HeadingLyrics       = 'How To Write Lyrics';
	const LyricsIntroduction1 = 'These are the ways to write the lyrics:';
	const LyricsIntroduction2 = '- If you have the original lyrics in the form of an image (a photo, screenshot, scan, etc), then just write them using it.';
	const LyricsIntroduction3 = '- Perhaps, you found a website having the lyrics. Yes, you may copy them. Find out if the website asks to reference it.';
	const LyricsIntroduction4 = '- If you have not found the lyrics and you are sure in your skills, then do it by listening.';
	const LyricsHeadsUp       = 'Keep in mind the following rules:';
	const LyricsRule1         = '1. Write lyrics in full. Do not use ※repeat or something alike.';
	const LyricsRule2         = '2. Write only lyrics. Do not write [Verse 1] or [Sings: Any Name] or anything like this.';
	const LyricsRule3         = '3. Do not censor lyrics even if there are bad words. Write them as they are.';
	const LyricsRule4         = '4. Do not fix the language. For example, a Japanese song might have an incorrect phrase in English. Write the phrase as it is, do not fix it.';
	const LyricsRule5         = '5. Correct mistakes in the lyrics if lyrics in the text source differ from the lyrics in the song.';
	const LyricsRule6         = '6. Listen to the song carefully and check whether your lyrics are correct.';
	const LyricsRule7         = '7. If you are unsure whether words or opinions expressed in the song are legal, contact us at support@vn-song-lyrics-db.ru.';
	const LyricsRule8         = '8. Read how to format text on this website.';
	const LyricsRule9         = '9. Use the “Notes” section if you want to add a comment or explanation.';
	
	const HeadingTranslation = 'How To Translate Lyrics';
	
	const TranslationIntroduction = 'The mistake of the beginning language learner is to find words in a dictionary and connect them directly to their translations. This is wrong way.';
	
	const TranslationRemember1 = 'A good translator has to keep in mind:';
	const TranslationRemember2 = '- cultural aspects;';
	const TranslationRemember3 = '- original sounding;';
	const TranslationRemember4 = '- good phrasing;';
	const TranslationRemember5 = '- all kinds of tropes;';
	const TranslationRemember6 = '- styles of speech;';
	const TranslationRemember7 = '- and, of course, the original meaning.';
	const TranslationRemember8 = 'Maybe, there is something more to that? Good if you answered yes.';
	
	const TranslationHeadsUp = 'Keep in mind the following:';
	const TranslationRule1   = '1. No AI or translation engines. Use your head.';
	const TranslationRule2   = '2. Use modern vocabularies that give definitions in the original language: explanations are better than a straight answer given by another translator. '.
	                           'Not to mention that this another translator may be wrong or their answer may be outdated.';
	const TranslationRule3   = '3. Use websites dedicated to help language acquisition: e.g. stackexchange.com, hinative.com and others. Explanations made by modern native speakers may become really useful.';
	const TranslationRule4   = '4. Find more examples of use when in doubt: texts and posts in the Internet, songs and poems, vocabularies and databases, Youtube videos and shorts. Use everything.';
	const TranslationRule5   = '5. Make a search engine your friend: learn how to use its features. For example, use straight quotation marks to search the exact word or phrase: "I love you" instead of I love you.';
	const TranslationRule6   = '6. Use typographic symbols: “curly quotation marks”, «guillemets», ellipsis… and other symbols. Make your translation a fine piece of art.';
	
	const TranslationLanguages = 'Here should be a small remark. Perhaps, there is no target language on the website. Send us a mail or ask us to add a language in Feedback section.';
	
	const HeadingRomanization = 'How To Romanize Lyrics And Names';
	
	const RomanizationIntroduction = 'This section is dedicated to romanization of Japanese language as it is the language of the most of the songs. '.
	                                 'Romanization of other languages is not covered, so you may use any known romanization rules for them.';
	
	const RomanizationJapanese1 = 'So, about Japanese. Most probably, you heard about Hepburn romanization rules. On this website, another rules are used, though they look a bit similar. '.
	                              'The rules will be explained more in schemes and examples rather than in words. We hope it is more understandable.';
	const RomanizationJapanese2 = 'Before we start, a little reminder: you agreed to comply with these rules when signed up. Whether you like them or not.';
	
	const HeadingAllowedSymbols = 'Allowed Symbols';
	const AllowedSymbols        = 'Romanization uses only characters that you can see on your keyboard. This is the rule #1.';
	
	const HeadingNameOrder = 'Name Order';
	const NameOrder        = 'In Japan, first goes surname and only then name. This order must be preserved in romanization.';
	
	const HeadingCapitalization = 'Capitalizing Words';
	const Capitalization        = 'All words, except for particles, must be capitalized in names.';
	
	const KanaConversionRules = 'Kana Conversion Rules';
	const KanaConversionNote  = 'Important Notes:';
	
	const KanaLanguageRules = 'Transliterating Katakana, Hiragana, Kanji, Other Languages';
	const KanaLanguageRule1 = '1. Words written in Japanese are written in lowercase, according to the table above.';
	const KanaLanguageRule2 = '2. Loanings are never replaced with words they were derived from.';
	const KanaLanguageRule3 = '3. Words written not in Japanese are written in uppercase.';
	
	const ParticlesRules      = 'Together or Apart';
	const Together            = 'together';
	const Apart               = 'apart';
	const DifferenceInMeaning = 'Difference in Meaning';
	const SpecialReadings     = 'Special Readings';
	const DivisionBySyllables = 'Separated Syllables';
	const OldWritingStyle     = 'Old Writing Style';
	
	const HeadingFormatting      = 'How To Apply Formatting';
	const FormattingIntroduction = 'The text editor of the website supports special syntax to allow use of:';
	const FormattingEntity1      = '- furigana;';
	const FormattingEntity2      = '- colored text;';
	const FormattingEntity3      = '- notes.';
	
	const FormattingFurigana1 = '1. Furigana is created with following syntax:';
	const FormattingFurigana2 = ', where * is any number of any symbols.';
	const FormattingFurigana3 = 'Example:';
	const FormattingFurigana4 = 'Use-case: irregular, special or ambiguous readings, rare words.';
	
	const FormattingColor1 = '2. Colored text is created with following syntax:';
	const FormattingColor2 = ', where XXXXXX is a hexadecimal value of RGB color and where * is any number of any symbols.';
	const FormattingColor3 = 'Example:';
	const FormattingColor4 = 'Use-case: several performers with different lines.';
	
	const FormattingNotes1 = '3. Notes are created with following syntax:';
	const FormattingNotes2 = ', where N is any natural number. Please, note that this string must be put both in notes and lyrics.';
	const FormattingNotes3 = 'Example: ';
	const FormattingNotes4 = 'Use-case: lyrics and notes.';
	
	const FormattingExample1 = 'If you are interested in use of these features, take a look at the ';
	const FormattingExample2 = 'example';
	const FormattingExample3 = '.';
	
	const FormattingWarnings = 'Please, note that these features are available only in the following sections:';
	const FormattingWarning1 = '- “Lyrics”;';
	const FormattingWarning2 = '- “Notes”;';
	const FormattingWarning3 = '- “Translation of Lyrics”;';
	const FormattingWarning4 = '- “Translator’s Notes”.';
	const FormattingWarning5 = 'We do not impose use of these features. Use them if you want to.';
	
	const HeadingCaptcha = 'How To Solve Captcha';
}

namespace Localization\LyricsExamplePage
{
	const Heading    = 'Example of Lyrics and Formatting';
	const Formatting = 'Formatting';
	const Japanese   = 'Japanese';
	const Markup     = 'Markup';
	const Result     = 'Result';
	const Notes      = 'Notes';
}

namespace Localization\ErrorPage\BadRequest400
{
	const Reason = 'You entered something either forbidden or invalid.';
	const Hint   = 'Please, go back and check the input one time more.';
}

namespace Localization\ErrorPage\Unauthorized401
{
	const Reason = 'Only authorized users may have access to this page.';
	const Hint   = 'Make sure you are logged in.';
}

namespace Localization\ErrorPage\PaymentRequired402
{
	const Reason = 'Only those who pay for the server are allowed to visit this page.';
	const Hint   = '~♪~ Have a good day ~♪~';
}

namespace Localization\ErrorPage\Forbidden403
{
	const Reason = 'You are not allowed to visit this page.';
	const Hint   = 'There is nothing you can do to get access.';
}

namespace Localization\ErrorPage\NotFound404
{
	const Reason = 'The requested page was not found.';
	const Hint   = 'Perhaps, it was renamed?';
}

namespace Localization\ErrorPage\MethodNotAllowed405
{
	const Reason = 'The server understood your request, but the selected method is not allowed.';
	const Hint   = 'Please, do not misuse the service.';
}

namespace Localization\ErrorPage\NotAcceptable406
{
	const Reason = 'The server does not support the language you selected.';
	const Hint   = 'Select one of the available languages in the header of the page.';
}

namespace Localization\ErrorPage\Conflict409
{
	const Reason = 'The content you tried to add already exists in the database.';
	const Hint   = 'Please, check the existence of the content before you add it.';
}

namespace Localization\ErrorPage\ContentTooLarge413
{
	const Reason = 'The file you tried to upload was too large.';
	const Hint   = 'Please, go back and choose another file.';
}

namespace Localization\ErrorPage\UnsupportedMediaType415
{
	const Reason = 'The file you tried to upload has an incorrect type.';
	const Hint   = 'Please, go back and choose another file.';
}

namespace Localization\ErrorPage\UnprocessableEntity422
{
	const Reason = 'Your input failed to pass validation rules on the server.';
	const Hint   = 'Please, send a mail to support@vn-song-lyrics-db.ru with details.';
}

namespace Localization\ErrorPage\UnavailableForLegalReasons451
{
	const Reason = 'The website got a legal note to restrict access to this page.';
	const Hint   = 'There is nothing you can do to get access.';
}

namespace Localization\ErrorPage\InternalServerError500
{
	const Reason = 'The server has encountered an error while processing your request.';
	const Hint   = 'Please, send a mail to support@vn-song-lyrics-db.ru with details.';
}

namespace Localization\ErrorPage\NotImplemented501
{
	const Reason = 'The method to complete your request is unknown.';
	const Hint   = 'Please, do not misuse the service.';
}

namespace Localization\ErrorPage\BadGateway502
{
	const Reason = 'The server received an error from another one.';
	const Hint   = 'Please, try again later.';
}

namespace Localization\ErrorPage\ServiceUnavailable503
{
	const Reason = 'The website is not available right now due to maintenance.';
	const Hint   = 'Please, visit us later.';
}
