import React from 'react';
import axios from 'axios';
import Select, { Creatable } from 'react-select';

class EditCategories extends React.Component
{
    constructor(props)
    {
        super(props);
        
        this.state = {
            category: '',
            subcategories: [],

            availableSubcategories: [],
            isFetchingSubcategories: false,
        }

        this.handleCategoryChange = this.handleCategoryChange.bind(this);
        this.handleSubcategoryChange = this.handleSubcategoryChange.bind(this);        
    }

    componentDidMount() {
        let category = this.props.currentCategory;
        let subcategories = [];
        
        this.props.currentSubcategories.map(subcategory => {
            subcategories.push({
                value: subcategory.name,
                label: subcategory.name,
            })
        })

        this.setState({ category, subcategories })
        this.fetchSubcategories(category);
    }

    handleCategoryChange(e) {
        this.setState({
            category: e.value, // 1
            isFetchingSubcategories: true,
        });
        
        this.props.handleCategoryChange(e.value);

        this.fetchSubcategories(e.value);
    }

    fetchSubcategories(category) {
        let that = this;
        let url = '/api/categories/'+category+'/subcategories';

        axios.get(url)
            .then(function(response) {
                that.setState({
                    isFetchingSubcategories: false,
                    availableSubcategories: response.data,
                });
            })
    }

    handleSubcategoryChange(value) {
        this.setState({ subcategories: value })
        this.props.handleSubcategoryChange(value);        
    }

    render()
    {
        let availableCategories = [];
        app.categories.map(category => {
            availableCategories.push({
                value: category.id,
                label: category.name
            });
        })

        let availableSubcategories = this.state.availableSubcategories.map(subcategory => {
            return {
                value: subcategory.name,
                label: subcategory.name
            }
        })

        let errors = this.props.errors;
        let formGroup = 'form-group';
        let hasError = formGroup + ' has-error';

        let categoryClass = errors.hasOwnProperty('category') ? hasError : formGroup;
        let subcategoryClass = errors.hasOwnProperty('subcategories') ? hasError : formGroup;

        return (
            <div className="row">
                <div className="col-md-6">
                    <div className={categoryClass}>
                        <label htmlFor="category" className="control-label">Category</label>
                        <Select
                            name="category"
                            value={this.state.category}
                            options={availableCategories}
                            onChange={this.handleCategoryChange} />
                    </div>
                </div>
                <div className="col-md-6">
                    <div className={subcategoryClass}>
                        <label htmlFor="category" className="control-label">Subcategories</label>
                        <Creatable
                            name="subcategories"
                            value={this.state.subcategories}
                            options={availableSubcategories}
                            isLoading={this.state.isFetchingSubcategories}
                            multi={true}
                            joinValues
                            onChange={this.handleSubcategoryChange} />
                    </div>
                </div>
            </div>
        )
    }
}

export default EditCategories;
