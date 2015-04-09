<?php namespace App\Http\Controllers;

use App\BaseString;
use App\Http\Requests\StringAdminRequest;
use App\Http\Requests\StringRequest;
use App\Http\Requests\StringVoteRequest;
use App\Language;
use App\Log;
use App\Project;
use App\String;
use App\Vote;
use Auth;
use Request;

class TranslationsController extends BaseApiController {

	public function __construct() {
		$this->middleware( 'auth' );
	}

	public function index() {
		$projects  = Project::orderBy( 'name' )->lists( 'name', 'id' );
		$languages = Language::orderBy( 'name' )->lists( 'name', 'id' );

		return view( 'translations/index', compact( 'projects', 'languages' ) );
	}

	public function baseStrings() {
		$baseStrings = BaseString::where( 'project_id', '=', Request::get( 'project_id' ) )->get( [
			'id',
			'key',
			'text'
		] );

		return $this->respond( $baseStrings );
	}

	public function strings() {
		$baseStrings = String::where( 'project_id', '=', Request::get( 'project_id' ) )->where( 'language_id', '=', Request::get( 'language_id' ) )->get();

		return $this->respond( $baseStrings );
	}

	public function checkPrivileges() {
		$language = Language::findOrFail( Request::get( 'language_id' ) );

		$result = Auth::user()->hasRole( $language->name . ' admin' ) || Auth::user()->hasRole( 'Root' );

		return $this->respond( $result );
	}

	public function store( StringRequest $request ) {
		$input            = Request::all();
		$input['user_id'] = Auth::user()->id;

		$string = String::create( $input );

		$baseString = BaseString::findOrFail( Request::get( 'base_string_id' ) )->key;
		Log::create( [
			'project_id' => Request::get( 'project_id' ),
			'user_id'    => Auth::user()->id,
			'text'       => Auth::user()->name . ' translated ' . $baseString . ' to ' . Request::get( 'text' )
		] );

		return $this->respond( $string );
	}

	public function vote( StringVoteRequest $request ) {
		$string = String::findOrFail( Request::get( 'string_id' ) );

		$vote = Vote::firstOrNew( [
			'string_id' => Request::get( 'string_id' ),
			'user_id'   => Auth::user()->id
		] );

		if ( $vote->id ) {
			return $this->respondWithError( 'Already voted!' );
		}

		$vote->save();

		$voteType = ( Request::get( 'vote' ) == 1 ) ? 'up_votes' : 'down_votes';
		$string->update( [ $voteType => $string[ $voteType ] + 1 ] );

		return $this->respond( 'Vote saved.' );
	}

	public function accept( StringAdminRequest $request ) {
		$string = String::where( [
			'id'          => Request::get( 'string_id' ),
			'language_id' => Request::get( 'language_id' )
		] )->firstOrFail();

		String::where( [ 'base_string_id' => Request::get( 'base_string_id' ) ] )->update( [
			'is_accepted' => false
		] );

		$string->update( [ 'is_accepted' => true ] );

		return $this->respond( 'String accepted.' );
	}

	public function trash( StringAdminRequest $request ) {
		$string = String::findOrFail( [
			'id'          => Request::get( 'string_id' ),
			'language_id' => Request::get( 'language_id' )
		] );

		$string->delete();

		return $this->respond( 'String deleted.' );
	}

}