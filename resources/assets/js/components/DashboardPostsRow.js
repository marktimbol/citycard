import React from 'react';

class DashboardPostsRow extends React.Component
{
    render()
    {
        let post = this.props.post;
        let checked = this.props.checked;
        let url = '/dashboard/posts/' + post.id;

        let merchant_url = '/dashboard/merchants/' + post.merchant.id;

        let outlets = post.outlets.map(outlet => {
            let outlet_url = '/dashboard/merchants/' + post.merchant.id + '/outlets/' + outlet.id; 
            return (
                <span className="label label-success" key={outlet.id}>
                    <a href={outlet_url}>
                        {outlet.name}
                    </a>
                </span>
            )
        })

        let sources = post.sources.map(source => {
            let source_posts_url = '/dashboard/sources/' + source.id + '/posts';
            return (
                <span className="label label-success" key={source.id}>
                    <a href={source_posts_url}>
                        {source.name}
                    </a>
                </span>
            )
        })        

        return (
            <tr>
                <td>
                    <div className="checkbox">
                        <label>
                            <input type="checkbox" name="posts[]" value={post.id} checked={checked} onChange={() => this.props.checkRow(post.id, ! checked)} />
                            <a href={url}>{post.title}</a>
                        </label>
                    </div>
                </td>
                <td>{post.type}</td>
                <td>
                    <a href={merchant_url}>
                        {post.merchant.name}
                    </a>
                </td>
                <td>{sources}</td>
                <td>{outlets}</td>
            </tr>
        )
    }
}

export default DashboardPostsRow;
