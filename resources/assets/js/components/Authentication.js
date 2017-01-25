// Authentication.js
import React from 'react';
import ReactDOM from 'react-dom';
import RegisterUser from './RegisterUser';
import LoginUser from './LoginUser';
import ForgotPassword from './ForgotPassword';

class Authentication extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			showRegister: true,
			showLogin: false,
			showForgotPassword: false,
			test: false,
		}

		this.showLogin = this.showLogin.bind(this);
		this.showRegister = this.showRegister.bind(this);
		this.showForgotPassword = this.showForgotPassword.bind(this);
	}

	showLogin() {
		this.setState({
			showLogin: true,
			showRegister: false,
			showForgotPassword: false
		})
	}

	showRegister() {
		this.setState({
			showRegister: true,
			showLogin: false,
			showForgotPassword: false
		})
	}	

	showForgotPassword() {
		this.setState({
			showForgotPassword: true,
			showRegister: false,
			showLogin: false,
		})
	}	

	render()
	{
		return (
			<div className="Authentication">
				{ this.state.showRegister ? <RegisterUser showLogin={this.showLogin} /> : <span></span> }
				{ this.state.showLogin ? <LoginUser showRegister={this.showRegister} showForgotPassword={this.showForgotPassword} /> : <span></span> }
				{ this.state.showForgotPassword ? <ForgotPassword showLogin={this.showLogin} /> : <span></span> }
			</div>
		)
	}
}

ReactDOM.render(
	<Authentication />,
	document.getElementById('Authentication')
)