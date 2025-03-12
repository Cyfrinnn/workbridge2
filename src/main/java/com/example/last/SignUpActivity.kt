package com.example.last

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.RelativeLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import okhttp3.*
import org.json.JSONObject
import java.io.IOException
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.RequestBody.Companion.toRequestBody

class SignUpActivity : AppCompatActivity() {

    private var isApplicantSignUp = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_sign_up)

        val applicantButton: Button = findViewById(R.id.btn_applicant)
        val employerButton: Button = findViewById(R.id.btn_employer)
        val signUpButton: Button = findViewById(R.id.btn_signup)
        val loginTextView: TextView = findViewById(R.id.tv_login)

        val fullNameEditText: EditText = findViewById(R.id.et_full_name)
        val companyNameEditText: EditText = findViewById(R.id.et_company_name)

        // Applicant fields
        val applicantEmailEditText: EditText = findViewById(R.id.et_applicant_email)
        val applicantPasswordEditText: EditText = findViewById(R.id.et_applicant_password)
        val applicantConfirmPasswordEditText: EditText = findViewById(R.id.et_applicant_confirm_password)

        // Employer fields
        val employerEmailEditText: EditText = findViewById(R.id.et_employer_email)
        val employerPasswordEditText: EditText = findViewById(R.id.et_employer_password)
        val employerConfirmPasswordEditText: EditText = findViewById(R.id.et_employer_confirm_password)

        applicantButton.setOnClickListener {
            isApplicantSignUp = true
            toggleSignUpFields(fullNameEditText, companyNameEditText, applicantEmailEditText, applicantPasswordEditText, applicantConfirmPasswordEditText, employerEmailEditText, employerPasswordEditText, employerConfirmPasswordEditText, signUpButton)
            Toast.makeText(this, "Applicant selected", Toast.LENGTH_SHORT).show()
        }

        employerButton.setOnClickListener {
            isApplicantSignUp = false
            toggleSignUpFields(fullNameEditText, companyNameEditText, applicantEmailEditText, applicantPasswordEditText, applicantConfirmPasswordEditText, employerEmailEditText, employerPasswordEditText, employerConfirmPasswordEditText, signUpButton)
            Toast.makeText(this, "Employer selected", Toast.LENGTH_SHORT).show()
        }

        signUpButton.setOnClickListener {
            val nameOrCompany = if (isApplicantSignUp) fullNameEditText.text.toString() else companyNameEditText.text.toString()
            val email = if (isApplicantSignUp) applicantEmailEditText.text.toString() else employerEmailEditText.text.toString()
            val password = if (isApplicantSignUp) applicantPasswordEditText.text.toString() else employerPasswordEditText.text.toString()
            val confirmPassword = if (isApplicantSignUp) applicantConfirmPasswordEditText.text.toString() else employerConfirmPasswordEditText.text.toString()

            // Handle sign-up logic
            if (password == confirmPassword) {
                if (isApplicantSignUp) {
                    signUpApplicant(nameOrCompany, email, password)
                } else {
                    signUpEmployer(nameOrCompany, email, password)
                }
            } else {
                Toast.makeText(this, "Passwords do not match", Toast.LENGTH_SHORT).show()
            }
        }

        loginTextView.setOnClickListener {
            // Handle login link click
            val intent = Intent(this, LoginActivity::class.java)
            startActivity(intent)
        }
    }

    private fun toggleSignUpFields(
        fullNameEditText: EditText,
        companyNameEditText: EditText,
        applicantEmailEditText: EditText,
        applicantPasswordEditText: EditText,
        applicantConfirmPasswordEditText: EditText,
        employerEmailEditText: EditText,
        employerPasswordEditText: EditText,
        employerConfirmPasswordEditText: EditText,
        signUpButton: Button
    ) {
        if (isApplicantSignUp) {
            fullNameEditText.visibility = View.VISIBLE
            companyNameEditText.visibility = View.GONE

            applicantEmailEditText.visibility = View.VISIBLE
            applicantPasswordEditText.visibility = View.VISIBLE
            applicantConfirmPasswordEditText.visibility = View.VISIBLE

            employerEmailEditText.visibility = View.GONE
            employerPasswordEditText.visibility = View.GONE
            employerConfirmPasswordEditText.visibility = View.GONE

            signUpButton.layoutParams = (signUpButton.layoutParams as RelativeLayout.LayoutParams).apply {
                addRule(RelativeLayout.BELOW, R.id.et_applicant_confirm_password)
            }

        } else {
            fullNameEditText.visibility = View.GONE
            companyNameEditText.visibility = View.VISIBLE

            applicantEmailEditText.visibility = View.GONE
            applicantPasswordEditText.visibility = View.GONE
            applicantConfirmPasswordEditText.visibility = View.GONE

            employerEmailEditText.visibility = View.VISIBLE
            employerPasswordEditText.visibility = View.VISIBLE
            employerConfirmPasswordEditText.visibility = View.VISIBLE

            signUpButton.layoutParams = (signUpButton.layoutParams as RelativeLayout.LayoutParams).apply {
                addRule(RelativeLayout.BELOW, R.id.et_employer_confirm_password)
            }
        }
    }

    private fun signUpApplicant(fullName: String, email: String, password: String) {
        val client = OkHttpClient()

        val json = JSONObject().apply {
            put("full_name", fullName)
            put("email", email)
            put("password", password)
        }

        val body = json.toString().toRequestBody("application/json; charset=utf-8".toMediaType())
        val request = Request.Builder()
            .url("http://10.0.2.2/api/add_applicant.php") // Update this URL with your server address if needed
            .post(body)
            .build()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                runOnUiThread {
                    Toast.makeText(this@SignUpActivity, "Network error: " + e.message, Toast.LENGTH_SHORT).show()
                }
            }

            override fun onResponse(call: Call, response: Response) {
                if (response.isSuccessful) {
                    runOnUiThread {
                        Toast.makeText(this@SignUpActivity, "Sign up successful", Toast.LENGTH_SHORT).show()
                        // Navigate to the confirmation activity upon successful sign-up
                        val intent = Intent(this@SignUpActivity, ConfirmationActivity::class.java)
                        startActivity(intent)
                        finish()
                    }
                } else {
                    runOnUiThread {
                        Toast.makeText(this@SignUpActivity, "Sign up failed: " + response.message, Toast.LENGTH_SHORT).show()
                    }
                }
            }
        })
    }

    private fun signUpEmployer(companyName: String, email: String, password: String) {
        val client = OkHttpClient()

        val json = JSONObject().apply {
            put("company_name", companyName)
            put("email", email)
            put("password", password)
        }

        val body = json.toString().toRequestBody("application/json; charset=utf-8".toMediaType())
        val request = Request.Builder()
            .url("http://10.0.2.2/api/add_employer.php")
            .post(body)
            .build()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                runOnUiThread {
                    Toast.makeText(this@SignUpActivity, "Network error: " + e.message, Toast.LENGTH_SHORT).show()
                }
            }

            override fun onResponse(call: Call, response: Response) {
                if (response.isSuccessful) {
                    runOnUiThread {
                        Toast.makeText(this@SignUpActivity, "Sign up successful", Toast.LENGTH_SHORT).show()
                        // Navigate to the confirmation activity upon successful sign-up
                        val intent = Intent(this@SignUpActivity, ConfirmationActivity::class.java)
                        startActivity(intent)
                        finish()
                    }
                } else {
                    runOnUiThread {
                        Toast.makeText(this@SignUpActivity, "Sign up failed: " + response.message, Toast.LENGTH_SHORT).show()
                    }
                }
            }
        })
    }
}
