qimnet/crud-bundle
==================

This bundle provides tools for quickly creating simple CRUD interfaces.


Installation
============

Add qimnet/table-bundle to composer.json. Depending on your
``minimum_stability`` setting, you might need to also add its dependencies.


.. code-block:: javascript

    "require": {

        "qimnet/table-bundle": "~1.0@dev",
        "qimnet/paginator-bundle": "~1.0@dev",
        "qimnet/crud-bundle": "~1.0@dev"
    }


Add QimnetTableBundle, QimnetPaginatorBundle and QimnetCRUDBundle to your
application kernel.

.. code-block:: php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Qimnet\TableBundle\QimnetTableBundle(),
            new Qimnet\TableBundle\QimnetPaginatorBundle(),
            new Qimnet\TableBundle\QimnetCRUDBundle(),
            // ...
        );
    }

Finally, add the CRUD controller to your routing

.. code-block:: yaml

    qimnet_crud:
        resource: "@QimnetCRUDBundle/Resources/config/routing.yml"


Configuration
=============

Configuration example
---------------------

The bundle automatically creates configuration services for all configured
object classes. A configuration example could be the following :

.. code-block:: yaml

    qimnet_crud:
        defaults:
            options:
                security_context_options:
                    credentials:
                        index: ROLE_ADMIN
                        create: ROLE_ADMIN
                        update: ROLE_ADMIN
                        delete: ROLE_ADMIN

        services:
            administrator:
                options:
                    object_class: 'ACME\WebsiteBundle\Entity\Administrator'
                    form_type:  'administrator'
                    table_type: 'ACME\BackendBundle\Table\AdministratorType'

            configuration:
                options:
                    object_class: 'ACME\WebsiteBundle\Entity\Configuration'
                    form_type:  'ACME\BackendBundle\Form\ConfigurationType'
                    id_column: locale
                    security_context_options:
                        credentials:
                            index: ROLE_ADMIN
                            update: ROLE_ADMIN

This configuration would create two CRUD configuration services.

* a full CRUD interface accessible for the Administrator entity,
  at http://localhost/backend/administrator

* a CRUD interface limited to listing and editing for the Configuration entity
  at http://localhost/backend/configuration


Configuration options
---------------------

The following options are available with the default CRUDConfigurationInterface
implementation:

form_type:
  Can contain the name of a form type service or a form type class name.
  This options is **required** if the edit or create actions are used.

table_type:
  Can contain the name of a table type service or a table type class name.
  This options is **required** if the edit or create actions are used.
  Please see the `Table types`_ section for more details.

table_type:
  Can contain the name of a filter type service or a filter type class name.
  Please see the `Filter types`_ section for more details.

object_class:
  The class of the managed objects. This option is **required**.

base_template
  The twig template from which all CRUD templates inherit.

edit_template
  The twig template for the edit action.

new_template
  The twig template for the create action.

form_template
  The form template included in the edit and create action templates.

index_template
  The twig template used for the index action.

show_template
  The twig template for the show action. If left blank, the show action will
  be deactivated.

edit_title
  The title of the edit page.

new_title
  The title of the create page.

index_title
  The title of the index page.

limit_per_page
  The maximum number of objects displayed on the index page.

route_prefix
  The prefix for crud route names.

csrf_intention
  The CSRF intention used to generate tokens for all forms and actions.

id_column
  The name of the id column for the object.

object_creation_parameters
  Contains an array of request arguments names for the create action. If
  the corresponding request arguments exist, they will be set as properties
  in the created object.

object_manager_class
  The class for the object manager instance.

object_manager_options
  See the `Object manager options`_ section.

paginator_type
  The paginator type. See the `qimnet/paginator-bundle documentation
  <https://github.com/qimnet/paginator-bundle/blob/master/Resources/doc/index.rst>`_.

paginator_options
  The default paginator options. See the `qimnet/paginator-bundle documentation
  <https://github.com/qimnet/paginator-bundle/blob/master/Resources/doc/index.rst>`_.

security_context_class
  The class for the security context instance.

security_context_options
  See the `Security context options` section.

sort_link_renderer_options
  The options for the renderer of the sort links on the index page.
  Pleas read the  `qimnet/table-bundle documentation
  <https://github.com/qimnet/table-bundle/blob/master/Resources/doc/index.rst>`_

path_generator_class
  The class for the path generator instance



Security context options
------------------------

The default security context implementation allows the following option :

credentials:
  an array containing CRUD actions as keys and credentials as values. If a
  no credential is set for a CRUD action, the corresponding action is
  deactivated. The following actions can be used :
    - create
    - delete
    - show
    - index

.. WARNING::

    By default, credentials for all actions are set to
    ``IS_AUTHENTICATED_ANONIMOUSLY``. To secure all actions, please give default
    credentials equivalent to those given in the `Configuration example`_.



Object manager options
----------------------

For the moment, the only implemented object manager is the doctrine one. This
object manager accepts the following options:

query_builder_method:
  The repository method used to create a query builder for the index page. This
  method should receive the root entity alias as first argument.

entity_alias:
  The root entity alias for the index page query.

id_column:
  The name of the id column for the entity. *(automatically set by the default
  Configuration implementation)*


Table types
-----------

For the basics about creating table types, please read the `qimnet/table-bundle
documentation
<https://github.com/qimnet/table-bundle/blob/master/Resources/doc/index.rst>`_

The CRUD bundle defines two more column rendering strategies :

crud_link
  creates a link towards the main action (edit or show depending on your
  configuration).

sort_link
  used to render header sort links.

A typical table type class might resemble this :

.. code-block:: php

    <?php

    namespace ACME\WebsiteBundle\Table;

    use Qimnet\TableBundle\Table\TableTypeInterface;
    use Qimnet\TableBundle\Table\TableBuilderInterface;

    class AdministratorType implements TableTypeInterface
    {
        public function buildTable(TableBuilderInterface $builder)
        {
            $builder
                    ->add('id','crud_link')
                    ->add('username','crud_link')
                    ->add('date');
        }
    }

    

Filter types
------------

Filter types are standard Symfony form types. The CRUD bundle adds an extra
``filter_options`` to all form types, which can be used by the entity manager
to filter the index results.

The following options are used by the included doctrine entity manager :

column_name
  the column the entity should be filtered upon.

callback
  a callback function used to filter the query. The callback should have the
  following signature :

    .. code-block:: php

        function($queryBuilder, $columnName, $value, $filterOptions) { }


A typical filter type could look this way :

.. code-block:: php

    <?php
    namespace ACME\BackendBundle\Filter;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class MyFilterType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                    ->add('key1', 'choice', array(
                        'choices'=>array(''=>'', '0'=>'val0','1'=>'val1')
                    ))
                    ->add('key2', 'choice', array(
                        'choices'=>array(''=>'', '0'=>'val0','1'=>'val1'),
                        'filter_options'=>array(
                            'callback'=>function($queryBuilder, $column,
                                                 $value, $options) {
                                $queryBuilder
                                    ->andWhere('t2.key1 > :filter_value')
                                    ->setParameter('filter_value', $value);
                            }
                        )
                    ));
        }
        public function getName()
        {
            return 'locale';
        }
        public function getParent()
        {
            return 'my_filter';
        }
    }
