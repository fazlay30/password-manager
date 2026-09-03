import React from 'react'
import { Box, Button, Typography } from '@mui/material'
import { Formik } from 'formik'
import { CredentialFields } from '@/data/credentialFields'
import TextFieldCommon from './TextFieldCommon'
import { addGroupCredential, addUserCred, editGropCred, editUserCred } from '@/lib/api'
import { useToast } from '@/hooks/toast'

const AddEditForm = ({
  action = 'add',
  setOpen,
  defaultValues,
  fetchUserCreds,
  id=null
}) => {
  const { showToast } = useToast()

  const handleClose = () => setOpen(false)

  // initial field values
  const initialValues = {
    site_name: defaultValues?.site_name ?? "",
    site_url: defaultValues?.site_url ?? "",
    username: defaultValues?.username ?? "",
    password: defaultValues?.password ?? "",
  }

  // validation
  const handleValidateForm = (values) => {
    const errors = {}
    if (!values.site_name) {
      errors.site_name = 'Required'
    } else if (!values.site_url) {
      errors.site_url = 'Required'
    } else if (!values.username) {
      errors.username = 'Required'
    } else if (!values.password) {
      errors.password = 'Required'
    }
    return errors
  }

  // submit form
  const handleSubmitForm = async (values, setSubmitting) => {
    try {
      // edit
      if (action === 'editGrpCred' && defaultValues?.group_project) {
        // group cred edit
        const updateResponse = await editGropCred(defaultValues?.fk_group_project_id, defaultValues?.id, values)
        if (updateResponse?.success) {
          showToast("Credential updated successfully!", "success")
          fetchUserCreds()
          handleClose()
        }
        setSubmitting(false)
      } else if (action === 'edit') {
        const updateResponse = await editUserCred(defaultValues?.id, values)
        if (updateResponse?.success) {
          showToast("Credential updated successfully!", "success")
          fetchUserCreds()
          handleClose()
        }
        setSubmitting(false)
      } else if (action === 'add') {
        const addResponse = await addUserCred(values)
        if (addResponse?.success) {
          showToast("Credential added successfully!", "success")
          fetchUserCreds()
          handleClose()
        }
        setSubmitting(false)
      } else if (action === 'addGrpCred') {
        const addResponse = await addGroupCredential(id, values)
        if (addResponse?.success) {
          showToast("Credential added successfully!", "success")
          fetchUserCreds()
          handleClose()
        }
        setSubmitting(false)
      }
    } catch (err) {
      showToast("Credential action failed!", "error")
      setSubmitting(false)
    }
  }

  return (
    <Formik
      initialValues={initialValues}
      validate={values => handleValidateForm(values)}
      onSubmit={(values, { setSubmitting }) => handleSubmitForm(values, setSubmitting)}
    >
      {({
        values,
        errors,
        touched,
        handleChange,
        handleBlur,
        handleSubmit,
        isSubmitting,
        setFieldValue
      }) => (
        <Box
          component="form"
          onSubmit={handleSubmit}
          mb={2}
        >
          {
            CredentialFields.map((field, i) => (
              field?.type === "file" ?
                <Box key={i} mb={2}>
                  <Typography fontWeight="500" sx={{ mb: 1 }}>{field.label}:</Typography>
                  <input
                    name={field.name}
                    type="file"
                    onChange={(event) => {
                      setFieldValue(field?.name, event.currentTarget.files[0])
                    }}
                  />
                  <Typography component={"span"} color={"primary.main"}>
                    {errors[field.name] && touched[field.name] && errors[field.name]}
                  </Typography>
                </Box> :
                <Box key={i} mb={2}>
                  <TextFieldCommon
                    id={field.id}
                    label={field.label}
                    variant={'outlined'}
                    type={field.type}
                    name={field.name}
                    requiredStatus={field.required}
                    disabledStatus={field.disabled ?? false}
                    value={values[field.name]}
                    onchangeInputValue={handleChange}
                  />
                  <Typography component={"span"} color={"primary.main"}>
                    {errors[field.name] && touched[field.name] && errors[field.name]}
                  </Typography>
                </Box>
            ))
          }
          <Button
            variant="contained"
            type="submit"
            color="primary"
            disabled={isSubmitting}
            sx={{mt: 2, width: '100%'}}
          >Submit</Button>
        </Box>
      )}
    </Formik>
  )
}

export default AddEditForm