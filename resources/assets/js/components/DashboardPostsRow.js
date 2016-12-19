import React from 'react';
import TimeAgo from 'react-timeago';

class DashboardPostsRow extends React.Component
{
    render()
    {
        let post = this.props.post;
        let checked = this.props.checked;
        let url = '/dashboard/posts/' + post.id;

        return (
            <tr>
                <td>
                    <div className="checkbox">
                        <label>
                            <input type="checkbox" name="posts[]" value={post.id} checked={checked} onChange={() => this.props.checkRow(post.id, ! checked)} />
                            <a href={url}>{post.title} Mark</a>
                        </label>
                    </div>
                </td>
                <td width="100">
                    <TimeAgo date={post.created_at} />
                </td>
            </tr>
        )
    }
}

export default DashboardPostsRow;
