<?php
App::uses('BaseAuthenticate', 'Controller/Component/Auth');

/**
 *	複数のユーザー名でログイン可能にします。
 *	基本はFormAuthenticateと同じ仕様ですが、新たな設定値username_columnsに、配列で対象カラム名を指定することによって
 *	そのいずれかのカラム値とパスワードが一致した場合に、ユーザーを認証します。
 *
 *	{{{
 *		$this->Auth->authenticate = array(
 *			'MultiUsername' => array(
 *				'username_columns' => array('username','email'),
 *			),
 *		);
 *	}}}
 */
class MultiUsernameAuthenticate extends BaseAuthenticate {

	public $settings = array(
		'fields' => array(
			'username' => 'username',
			'password' => 'password'
		),
		'userModel' => 'User',
		'scope' => array(),
		'recursive' => 0,
		'contain' => null,
		'username_columns' => array(),
	);

	public function authenticate(CakeRequest $request, CakeResponse $response) {
		$userModel = $this->settings['userModel'];
		list($plugin,$model) = pluginSplit($userModel);
		
		$fields = $this->settings['fields'];
		if (empty($request->data[$model])) {
			return false;
		}
		if (
			empty($request->data[$model][$fields['username']]) ||
			empty($request->data[$model][$fields['password']])
		) {
			return false;
		}
		return $this->_findUser(
			$request->data[$model][$fields['username']],
			$request->data[$model][$fields['password']]
		);
	}
	
	protected function _findUser($username, $password) {
		$userModel = $this->settings['userModel'];
		list($plugin, $model) = pluginSplit($userModel);
		$fields = $this->settings['fields'];
		$username_columns = $this->settings['username_columns'];
		$username_conditions = array();
		
		if(!empty($username_columns)) {
			$username_conditions['OR'] = array();
			foreach($username_columns as $column) {
				$username_conditions['OR'][$model . '.' . $column] = $username;
			}
		} else {
			$username_conditions[$model . '.' . $fields['username']] = $username;
		}
	
		$conditions = array(
			$username_conditions,
			$model . '.' . $fields['password'] => $this->_password($password),
		);
		
		if (!empty($this->settings['scope'])) {
			$conditions = array_merge($conditions, $this->settings['scope']);
		}
		$result = ClassRegistry::init($userModel)->find('first', array(
			'conditions' => $conditions,
			'recursive' => (int)$this->settings['recursive'],
			'contain' => $this->settings['contain'],
		));
		if (empty($result) || empty($result[$model])) {
			return false;
		}
		$user = $result[$model];
		unset($user[$fields['password']]);
		unset($result[$model]);
		return array_merge($user, $result);
	}	
}