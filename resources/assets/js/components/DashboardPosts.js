import React from 'react';
import ReactDOM from 'react-dom';
import TimeAgo from 'react-timeago';

class DashboardPosts extends React.Component
{
	constructor(props)
	{
		super(props);

		let rowState = [];

		app.posts.data.map(post => {
			rowState[post.id] = false
		});

		this.state = {
			posts: app.posts,
			action: 'publish',
			checkAll: false,
			rowState: rowState,
			selectedPosts: []
		}

		this.handleActionChange = this.handleActionChange.bind(this);
		this.toggleCheckAll = this.toggleCheckAll.bind(this);
		this.checkRow = this.checkRow.bind(this);
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

	toggleCheckAll() {
		console.log('Toggle Check all');

		let rowState = [];
		let checkState = ! this.state.checkAll;

		app.posts.data.map(post => {
			rowState[post.id] = checkState;
		})

		this.state.checkAll = checkState;

		this.setState({
			rowState: rowState,
			checkAll: this.state.checkAll
		});

	}

	checkRow(e) {
		let postId = e.target.value;

		this.state.rowState[postId] = postId;
		if( this.state.checkAll ) {
			this.state.checkAll = ! this.state.checkAll;
		}

		this.setState({
			rowState: this.state.rowState,
			checkAll: this.state.checkAll,
		})
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
								<input type="checkbox" name="posts[]" value={post.id} checked={this.state.rowState[post.id]} onChange={this.checkRow} />
								<a href={url}>{post.title}</a>
							</label>
						</div>
					</td>
					<td width="100">
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
											<input type="checkbox" 
												defaultChecked={this.state.checkAll}
												onChange={this.toggleCheckAll} />
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
