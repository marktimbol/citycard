import React from 'react';
import ReactDOM from 'react-dom';
import TimeAgo from 'react-timeago';

class DashboardPosts extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			posts: app.posts,
			action: 'publish',
			selectedPosts: []
		}

		this.handleActionChange = this.handleActionChange.bind(this);
		this.onSubmit = this.onSubmit.bind(this);
	}

	componentDidMount()
	{
		console.log(app.posts);
	}

	handleActionChange(option)
	{
		let action = option.target.value;
		console.log(action);

		this.setState({ action });
	}

	onSubmit(e)
	{
		e.preventDefault();
		console.log('Submitting form.');

		let that = this;
		if( this.state.action == 'publish' )
		{
			console.log('publish posts');
			$.ajax({
				type: 'POST',
			    url: '/dashboard/posts/publish',
				headers: {
			        "X-CSRF-Token": App.csrfToken,
			    },
				data: $('#Posts').serialize(),
			    success: function(result) {
					console.log('result', result);
					that.setState({
						posts: result.posts,
					})
			    }
			});
		}

		else if( this.state.action == 'unpublish')
		{
			console.log('unpublish posts');
			$.ajax({
				type: 'DELETE',
			    url: '/dashboard/posts/unpublish',
				headers: {
			        "X-CSRF-Token": App.csrfToken,
			    },
				data: $('#Posts').serialize(),
			    success: function(result) {
					console.log('result', result);
					that.setState({
						posts: result.posts,
					})
			    }
			});
		}
	}

	render()
	{
		let actionOptions = [
			{ value: 'publish', 'label': 'Publish Posts' },
			{ value: 'unpublish', 'label': 'Unpublish Posts' },
		]

		let actions = actionOptions.map(action => {
			return (
				<option value={action.value} key={action.value}>
					{action.label}
				</option>
			)
		});

		let posts = this.state.posts.data.map(post => {
			let url = '/dashboard/posts/' + post.id;

			return (
				<tr key={post.id}>
					<td>
						<div className="checkbox">
							<label>
								<input type="checkbox" name="posts[]" value={post.id} />
								<a href={url}>{post.title}</a>
							</label>
						</div>
					</td>
					<td>
						<TimeAgo date={post.created_at} />
					</td>
				</tr>
			)
		})

		return (
			<div>
				<form method="POST" onSubmit={this.onSubmit} id="Posts">
					<div className="row">
						<div className="col-md-4">
							<div className="form-group form-inline">
								<select name="action" className="form-control input-sm" onChange={this.handleActionChange}>
									{ actions }
								</select>
								<button className="btn btn-sm btn-primary">
								Apply
								</button>
							</div>
						</div>
					</div>
					<table className="table table-bordered">
						<thead>
							<tr>
								<th>
									<div className="checkbox">
										<label>
											<input type="checkbox" />
											Title
										</label>
									</div>
								</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							{ posts }
						</tbody>
					</table>
				</form>
			</div>
		)
	}
}

ReactDOM.render(
	<DashboardPosts />,
	document.getElementById('DashboardPosts')
);
